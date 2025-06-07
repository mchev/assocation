<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    protected $equipmentTypes = [
        'Sonorisation' => [
            'Enceintes' => [
                'names' => [
                    'Enceinte amplifiée 15"',
                    'Caisson de basse 18"',
                    'Enceinte retour de scène 12"',
                    'Système line array compact',
                    'Enceinte colonne active',
                ],
                'specs' => [
                    'puissance' => ['400W RMS', '600W RMS', '800W RMS', '1000W RMS'],
                    'poids' => ['12 kg', '15 kg', '18 kg', '25 kg'],
                    'connectique' => ['XLR, Jack', 'Speakon, XLR', 'XLR, RCA'],
                ],
                'prices' => [30, 80],
            ],
            'Tables de mixage' => [
                'names' => [
                    'Console de mixage numérique 16 voies',
                    'Table de mixage analogique 12 voies',
                    'Console DJ 2 voies',
                    'Mixette 6 voies portable',
                ],
                'specs' => [
                    'voies' => ['6 voies', '12 voies', '16 voies', '24 voies'],
                    'effets' => ['Reverb, Delay', '16 effets', '24 effets'],
                    'connexions' => ['USB, XLR', 'USB, MIDI, XLR'],
                ],
                'prices' => [35, 120],
            ],
        ],
        'Éclairage' => [
            'Projecteurs' => [
                'names' => [
                    'Projecteur LED RGBW',
                    'PAR LED 18x10W',
                    'Lyre Spot 200W',
                    'Projecteur LED COB 100W',
                ],
                'specs' => [
                    'puissance' => ['100W', '150W', '200W', '300W'],
                    'modes' => ['DMX, Auto, Sound', 'DMX 8-16 canaux'],
                    'angle' => ['25°', '45°', '60°'],
                ],
                'prices' => [25, 70],
            ],
        ],
        'Scène' => [
            'Structures' => [
                'names' => [
                    'Structure alu carrée 2m',
                    'Pont lumière 3m',
                    'Pied de levage 4m',
                    'Structure scène 2x1m',
                ],
                'specs' => [
                    'charge' => ['50 kg', '80 kg', '100 kg'],
                    'matériau' => ['Aluminium', 'Acier'],
                    'assemblage' => ['Manchon conique', 'Goupille'],
                ],
                'prices' => [20, 60],
            ],
        ],
        'Vidéo' => [
            'Projecteurs vidéo' => [
                'names' => [
                    'Vidéoprojecteur 5000 lumens',
                    'Projecteur HD 1080p',
                    'Vidéoprojecteur courte focale',
                    'Projecteur 4K compact',
                ],
                'specs' => [
                    'luminosité' => ['3000 lumens', '4000 lumens', '5000 lumens'],
                    'résolution' => ['Full HD', '4K', 'WUXGA'],
                    'contraste' => ['2000:1', '3000:1', '5000:1'],
                ],
                'prices' => [50, 150],
            ],
        ],
    ];

    public function definition(): array
    {
        // Récupérer une catégorie aléatoire
        $category = Category::inRandomOrder()->first();

        if (! $category) {
            // Si aucune catégorie n'existe, en créer une par défaut
            $category = Category::create([
                'name' => 'Matériel technique',
                'slug' => 'materiel-technique',
                'description' => 'Matériel technique divers',
                'order' => 0,
            ]);
        }

        // Trouver le type d'équipement correspondant à la catégorie
        $equipmentType = $this->getEquipmentTypeForCategory($category->name);

        // Si aucun type d'équipement ne correspond, prendre un type au hasard
        if (! $equipmentType) {
            $mainCategory = Arr::random(array_keys($this->equipmentTypes));
            $equipmentType = Arr::random($this->equipmentTypes[$mainCategory]);
        }

        $name = Arr::random($equipmentType['names']);
        $specs = [];
        foreach ($equipmentType['specs'] as $key => $values) {
            $specs[$key] = Arr::random($values);
        }

        $price = fake()->numberBetween($equipmentType['prices'][0] * 100, $equipmentType['prices'][1] * 100);

        return [
            'name' => $name,
            'description' => $this->generateDescription($name, $specs),
            'reference' => strtoupper(fake()->bothify('??-###??')),
            'brand' => fake()->company(),
            'model' => fake()->bothify('??#####'),
            'quantity' => fake()->numberBetween(1, 10),
            'rental_price' => $price,
            'deposit_amount' => $price * 10,
            'requires_deposit' => fake()->boolean(),
            'specifications' => $specs,
            'organization_id' => Organization::factory(),
            'depot_id' => Depot::factory(),
            'category_id' => $category->id,
        ];
    }

    protected function getEquipmentTypeForCategory($categoryName): ?array
    {
        foreach ($this->equipmentTypes as $mainCategory => $types) {
            if ($mainCategory === $categoryName) {
                return Arr::random($types);
            }
            if (array_key_exists($categoryName, $types)) {
                return $types[$categoryName];
            }
        }

        return null;
    }

    protected function generateDescription($name, $specs): string
    {
        $description = $name."\n\n";
        foreach ($specs as $key => $value) {
            $description .= ucfirst($key).' : '.$value."\n";
        }

        return $description;
    }
}
