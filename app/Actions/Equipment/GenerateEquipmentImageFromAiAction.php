<?php

namespace App\Actions\Equipment;

use App\Models\Equipment;
use App\Models\EquipmentImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

use function Laravel\Ai\agent;

class GenerateEquipmentImageFromAiAction
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Build a search query via AI, find a product image via SerpApi, then download and attach it.
     *
     * @param  string|null  $overrideSearchQuery  For tests: skip AI and use this query directly.
     * @return array{image: EquipmentImage, steps: array<int, array{label: string, status: string}>}
     */
    public function execute(Equipment $equipment, ?string $overrideSearchQuery = null): array
    {
        $steps = [];
        $equipment->loadMissing(['category']);

        $step1 = ['label' => 'Génération de la requête de recherche', 'status' => 'pending'];
        $steps[] = $step1;
        $searchQuery = $overrideSearchQuery ?? $this->getSearchQueryFromAi($equipment);
        $steps[0]['status'] = 'ok';

        $step2 = ['label' => 'Recherche d\'image sur le web', 'status' => 'pending'];
        $steps[] = $step2;
        $imageUrl = $this->findBestImageUrl($searchQuery);
        $steps[1]['status'] = 'ok';

        $step3 = ['label' => 'Téléchargement et enregistrement', 'status' => 'pending'];
        $steps[] = $step3;
        $imageContent = $this->downloadImage($imageUrl);

        $filename = Str::uuid().'.webp';
        $path = "equipments/{$equipment->id}/{$filename}";

        $tempPath = sys_get_temp_dir().'/'.Str::uuid().'.bin';
        file_put_contents($tempPath, $imageContent);

        try {
            $processedImage = $this->manager->read($tempPath)
                ->scaleDown(width: 1024)
                ->toWebp(80);

            Storage::disk('s3')->put($path, $processedImage->toFilePointer(), 'public');

            $nextOrder = (int) $equipment->images()->max('order') + 1;

            $image = $equipment->images()->create([
                'path' => $path,
                'original_name' => 'recherche-web.jpg',
                'mime_type' => 'image/webp',
                'size' => $processedImage->size(),
                'order' => $nextOrder,
            ]);
            $steps[2]['status'] = 'ok';

            return ['image' => $image, 'steps' => $steps];
        } finally {
            @unlink($tempPath);
        }
    }

    protected function getSearchQueryFromAi(Equipment $equipment): string
    {
        $instructions = 'Tu es un assistant qui génères des requêtes de recherche pour trouver une photo produit réelle (site fabricant, revendeur, fiche produit). Réponds UNIQUEMENT avec une requête de recherche courte et précise, en anglais, sans guillemets. Exemple: pour "Console Soundcraft 8 pistes" → "Soundcraft 8 track mixer product photo".';

        $context = $equipment->name;
        if ($equipment->brand) {
            $context .= ' — Marque: '.$equipment->brand;
        }
        if ($equipment->description) {
            $context .= ' — '.Str::limit(strip_tags($equipment->description), 300);
        }
        if ($equipment->category) {
            $context .= ' — Catégorie: '.$equipment->category->name;
        }

        $agent = agent(
            instructions: $instructions,
            messages: [],
            tools: [],
            schema: fn ($schema) => [
                'search_query' => $schema->string()->required(),
            ]
        );

        $response = $agent->prompt("Génère une requête de recherche Google Images pour trouver une photo produit correspondant exactement à ce matériel:\n\n{$context}");

        $query = trim($response['search_query'] ?? '');
        if ($query === '') {
            throw new RuntimeException('L\'IA n\'a pas pu générer une requête de recherche.');
        }

        return $query;
    }

    protected function findBestImageUrl(string $searchQuery): string
    {
        $apiKey = config('services.serpapi.key');
        if (! $apiKey) {
            throw new RuntimeException('SERPAPI_API_KEY est requis dans .env (compte gratuit sur serpapi.com, 250 recherches/mois).');
        }

        return $this->findBestImageUrlWithSerpApi($searchQuery, $apiKey);
    }

    /**
     * SerpApi Google Images — 250 recherches/mois gratuites. serpapi.com
     */
    protected function findBestImageUrlWithSerpApi(string $searchQuery, string $apiKey): string
    {
        $response = Http::timeout(20)
            ->get('https://serpapi.com/search', [
                'engine' => 'google_images',
                'q' => $searchQuery,
                'api_key' => $apiKey,
                'gl' => 'fr',
                'hl' => 'fr',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('SerpApi indisponible (HTTP '.$response->status().'): '.$response->body());
        }

        $data = $response->json();
        $results = $data['images_results'] ?? [];

        foreach ($results as $item) {
            $url = $item['original'] ?? null;
            if ($url && str_starts_with($url, 'http') && ! str_starts_with($url, 'x-raw-image')) {
                return $url;
            }
        }

        throw new RuntimeException('Aucune image trouvée pour la requête: '.$searchQuery);
    }

    protected function downloadImage(string $imageUrl): string
    {
        $response = Http::timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
                'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
            ])
            ->get($imageUrl);

        if (! $response->successful()) {
            throw new RuntimeException('Impossible de télécharger l\'image: '.$imageUrl);
        }

        $body = $response->body();
        if (strlen($body) < 1000) {
            throw new RuntimeException('Image téléchargée trop petite ou invalide.');
        }

        return $body;
    }
}
