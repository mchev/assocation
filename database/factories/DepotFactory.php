<?php

namespace Database\Factories;

use App\Models\Depot;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepotFactory extends Factory
{
    protected $model = Depot::class;

    protected $cities = [
        ['name' => 'Paris', 'postal_code' => '750', 'lat' => 48.8566, 'lng' => 2.3522],
        ['name' => 'Lyon', 'postal_code' => '690', 'lat' => 45.7578, 'lng' => 4.8320],
        ['name' => 'Marseille', 'postal_code' => '130', 'lat' => 43.2965, 'lng' => 5.3698],
        ['name' => 'Toulouse', 'postal_code' => '310', 'lat' => 43.6047, 'lng' => 1.4442],
        ['name' => 'Bordeaux', 'postal_code' => '330', 'lat' => 44.8378, 'lng' => -0.5792],
        ['name' => 'Nantes', 'postal_code' => '440', 'lat' => 47.2184, 'lng' => -1.5536],
        ['name' => 'Strasbourg', 'postal_code' => '670', 'lat' => 48.5734, 'lng' => 7.7521],
        ['name' => 'Lille', 'postal_code' => '590', 'lat' => 50.6292, 'lng' => 3.0573],
        ['name' => 'Rennes', 'postal_code' => '350', 'lat' => 48.1173, 'lng' => -1.6778],
        ['name' => 'Montpellier', 'postal_code' => '340', 'lat' => 43.6108, 'lng' => 3.8767],
    ];

    protected $streetTypes = [
        'Rue', 'Avenue', 'Boulevard', 'Place', 'Allée', 'Route', 'Impasse', 'Chemin',
    ];

    protected $streetNames = [
        'des Fleurs', 'de la République', 'de la Liberté', 'du Général de Gaulle',
        'Victor Hugo', 'Jean Jaurès', 'de la Paix', 'des Roses', 'du Commerce',
        'de l\'Église', 'des Écoles', 'de la Gare', 'des Lilas', 'du Moulin',
        'Pasteur', 'des Tilleuls', 'des Acacias', 'du Stade', 'des Platanes',
    ];

    protected $depotTypes = [
        'Dépôt', 'Entrepôt', 'Local', 'Stockage', 'Hangar', 'Magasin',
    ];

    public function definition()
    {
        $city = $this->faker->randomElement($this->cities);
        $streetNumber = $this->faker->buildingNumber();
        $streetType = $this->faker->randomElement($this->streetTypes);
        $streetName = $this->faker->randomElement($this->streetNames);
        $address = "{$streetNumber} {$streetType} {$streetName}";

        // Ajouter une variation aléatoire aux coordonnées pour éviter que tous les dépôts soient au même endroit
        $latVariation = $this->faker->randomFloat(4, -0.01, 0.01);
        $lngVariation = $this->faker->randomFloat(4, -0.01, 0.01);

        $depotType = $this->faker->randomElement($this->depotTypes);
        $organizationName = \App\Models\Organization::factory()->make()->name;

        return [
            'organization_id' => \App\Models\Organization::factory(),
            'name' => "{$depotType} {$organizationName} - {$city['name']}",
            'address' => $address,
            'city' => $city['name'],
            'postal_code' => $city['postal_code'].$this->faker->numberBetween(10, 99),
            'country' => 'FR',
            'latitude' => $city['lat'] + $latVariation,
            'longitude' => $city['lng'] + $lngVariation,
            'opening_hours' => $this->generateOpeningHours(),
            'is_active' => true,
        ];
    }

    protected function generateFrenchPhoneNumber()
    {
        $formats = [
            '01 ## ## ## ##',
            '02 ## ## ## ##',
            '03 ## ## ## ##',
            '04 ## ## ## ##',
            '05 ## ## ## ##',
            '09 ## ## ## ##',
        ];

        return $this->faker->numerify($this->faker->randomElement($formats));
    }

    protected function generateOpeningHours()
    {
        $standardHours = [
            'morning' => ['09:00-12:00'],
            'afternoon' => ['14:00-18:00'],
        ];

        $variations = [
            'morning' => [
                '08:30-12:00',
                '09:00-12:30',
                '09:30-12:30',
            ],
            'afternoon' => [
                '13:30-17:30',
                '14:00-18:00',
                '14:00-18:30',
            ],
        ];

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $hours = [];

        foreach ($days as $day) {
            // 80% de chance d'avoir des horaires standards
            if ($this->faker->boolean(80)) {
                $hours[$day] = $standardHours['morning'];
                // 90% de chance d'être ouvert l'après-midi
                if ($this->faker->boolean(90)) {
                    $hours[$day][] = $this->faker->randomElement($variations['afternoon']);
                }
            } else {
                // Horaires variables
                $hours[$day] = [
                    $this->faker->randomElement($variations['morning']),
                    $this->faker->randomElement($variations['afternoon']),
                ];
            }
        }

        // Samedi : 60% de chance d'être ouvert, uniquement le matin la plupart du temps
        if ($this->faker->boolean(60)) {
            $hours['saturday'] = [$this->faker->randomElement($variations['morning'])];
            // 20% de chance d'être ouvert l'après-midi aussi
            if ($this->faker->boolean(20)) {
                $hours['saturday'][] = $this->faker->randomElement($variations['afternoon']);
            }
        } else {
            $hours['saturday'] = [];
        }

        // Dimanche : fermé
        $hours['sunday'] = [];

        return $hours;
    }
}
