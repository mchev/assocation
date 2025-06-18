<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\HelloAssoOrganizationService;
use Illuminate\Console\Command;

class SyncHelloAssoOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'helloasso:sync-organizations {email : Email de l\'utilisateur}';

    /**
     * The console command description.
     */
    protected $description = 'Synchroniser les organisations HelloAsso d\'un utilisateur';

    public function __construct(
        protected HelloAssoOrganizationService $helloAssoOrganizationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Utilisateur avec l'email {$email} non trouvé.");

            return 1;
        }

        if (! $user->helloasso_token) {
            $this->error("L'utilisateur {$email} n'a pas de token HelloAsso valide.");

            return 1;
        }

        $this->info("Synchronisation des organisations HelloAsso pour {$user->name} ({$user->email})...");

        try {
            // Récupérer les organisations depuis l'API HelloAsso
            $organizations = $this->getHelloAssoOrganizations($user->helloasso_token);

            if (empty($organizations)) {
                $this->warn('Aucune organisation trouvée pour cet utilisateur.');

                return 0;
            }

            $this->info('Organisations trouvées : '.count($organizations));

            // Synchroniser les organisations
            $this->helloAssoOrganizationService->syncOrganizations($user, $organizations);

            $this->info('Synchronisation terminée avec succès !');
            $this->table(
                ['ID', 'Nom', 'Email', 'Ville'],
                collect($organizations)->map(fn ($org) => [
                    $org['id'] ?? 'N/A',
                    $org['name'] ?? 'N/A',
                    $org['email'] ?? 'N/A',
                    $org['city'] ?? 'N/A',
                ])
            );

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la synchronisation : '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Get organizations from HelloAsso API.
     */
    protected function getHelloAssoOrganizations(string $token): array
    {
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->get('https://api.helloasso.com/v5/users/me/organizations');

        if (! $response->successful()) {
            throw new \Exception('Erreur API HelloAsso : '.$response->body());
        }

        $data = $response->json();

        return $data['data'] ?? [];
    }
}
