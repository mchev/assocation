<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    protected $associationPrefixes = [
        'Association', 'Amicale', 'Club', 'Comité', 'Collectif',
        'Groupe', 'Union', 'Fédération', 'Ligue', 'MJC'
    ];

    protected $associationThemes = [
        'des Jeunes', 'de la Culture', 'des Arts', 'du Sport',
        'des Loisirs', 'de la Musique', 'du Théâtre', 'de la Danse',
        'des Fêtes', 'du Patrimoine', 'de l\'Animation', 'du Quartier',
        'de la Solidarité', 'de l\'Environnement'
    ];

    protected $associationLocations = [
        'de {city}', 'du {region}', '{city}', 'en {region}',
        'du Quartier {neighborhood}', 'de la Région {region}'
    ];

    protected $regions = [
        'Île-de-France', 'Auvergne-Rhône-Alpes', 'Nouvelle-Aquitaine',
        'Occitanie', 'Hauts-de-France', 'Grand Est', 'Provence-Alpes-Côte d\'Azur',
        'Pays de la Loire', 'Normandie', 'Bretagne', 'Bourgogne-Franche-Comté',
        'Centre-Val de Loire', 'Corse'
    ];

    protected $neighborhoods = [
        'Centre', 'Vieux Port', 'Saint-Michel', 'Bastide',
        'Croix-Rousse', 'Vieux Lyon', 'Marais', 'Montmartre',
        'République', 'Capucins', 'Chartrons', 'Saint-Pierre'
    ];

    protected $cities = [
        'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Bordeaux',
        'Nantes', 'Strasbourg', 'Lille', 'Rennes', 'Montpellier'
    ];

    protected $companyTypes = [
        'SARL', 'SAS', 'EURL', 'SA', 'SNC'
    ];

    protected $companyActivities = [
        'Événementiel', 'Production', 'Location', 'Spectacle',
        'Audiovisuel', 'Sonorisation', 'Éclairage', 'Technique',
        'Prestation', 'Animation'
    ];

    public function definition()
    {
        $isCompany = $this->faker->boolean(30); // 30% de chance d'être une entreprise
        $city = $this->faker->randomElement($this->cities);
        $region = $this->faker->randomElement($this->regions);

        if ($isCompany) {
            $name = $this->generateCompanyName($city);
            $description = $this->generateCompanyDescription();
        } else {
            $name = $this->generateAssociationName($city, $region);
            $description = $this->generateAssociationDescription();
        }

        return [
            'name' => $name,
            'description' => $description,
            'address' => $this->faker->streetAddress,
            'city' => $city,
            'postal_code' => $this->faker->postcode,
            'phone' => $this->generateFrenchPhoneNumber(),
            'email' => $this->generateOrganizationEmail($name),
            'website' => $this->generateWebsite($name),
            'logo_path' => null,
            'is_active' => true,
        ];
    }

    protected function generateAssociationName($city, $region)
    {
        $prefix = $this->faker->randomElement($this->associationPrefixes);
        $theme = $this->faker->randomElement($this->associationThemes);
        $location = $this->faker->randomElement($this->associationLocations);
        
        $location = str_replace(
            ['{city}', '{region}', '{neighborhood}'],
            [$city, $region, $this->faker->randomElement($this->neighborhoods)],
            $location
        );

        return "{$prefix} {$theme} {$location}";
    }

    protected function generateCompanyName($city)
    {
        $type = $this->faker->randomElement($this->companyTypes);
        $activity = $this->faker->randomElement($this->companyActivities);
        
        if ($this->faker->boolean) {
            // Format: [Nom] [Activité] ([Ville]) [Type]
            $name = $this->faker->lastName . ' ' . $activity;
            if ($this->faker->boolean(70)) {
                $name .= ' (' . $city . ')';
            }
            $name .= ' ' . $type;
        } else {
            // Format: [Activité] [Ville] [Type]
            $name = $activity . ' ' . $city;
            if ($this->faker->boolean(30)) {
                $name .= ' ' . $type;
            }
        }

        return $name;
    }

    protected function generateAssociationDescription()
    {
        $descriptions = [
            "Association culturelle organisant des événements tout au long de l'année.",
            "Structure associative dédiée à la promotion et au développement des activités culturelles.",
            "Association à but non lucratif proposant des activités et du matériel pour les événements locaux.",
            "Collectif associatif au service des organisateurs d'événements.",
            "Association participant activement à la vie culturelle et événementielle locale."
        ];

        return $this->faker->randomElement($descriptions) . ' ' . $this->faker->sentence();
    }

    protected function generateCompanyDescription()
    {
        $descriptions = [
            "Entreprise spécialisée dans la location et la prestation de matériel événementiel.",
            "Société de services techniques pour l'événementiel et le spectacle.",
            "Prestataire professionnel pour vos événements et manifestations.",
            "Expert en solutions techniques pour l'événementiel.",
            "Location de matériel professionnel pour l'événementiel et le spectacle."
        ];

        return $this->faker->randomElement($descriptions) . ' ' . $this->faker->sentence();
    }

    protected function generateFrenchPhoneNumber()
    {
        $formats = [
            '01 ## ## ## ##',
            '02 ## ## ## ##',
            '03 ## ## ## ##',
            '04 ## ## ## ##',
            '05 ## ## ## ##',
            '09 ## ## ## ##'
        ];
        return $this->faker->numerify($this->faker->randomElement($formats));
    }

    protected function generateOrganizationEmail($name)
    {
        $sanitizedName = strtolower(
            iconv('UTF-8', 'ASCII//TRANSLIT', $name) // Remove accents
        );
        $sanitizedName = preg_replace('/[^a-z0-9]+/', '.', $sanitizedName); // Replace special chars with dots
        $sanitizedName = trim($sanitizedName, '.'); // Remove leading/trailing dots
        
        $domains = ['gmail.com', 'yahoo.fr', 'orange.fr', 'free.fr', 'laposte.net'];
        $domain = $this->faker->randomElement($domains);
        
        return substr($sanitizedName, 0, 20) . '@' . $domain;
    }

    protected function generateWebsite($name)
    {
        if ($this->faker->boolean(70)) { // 70% chance to have a website
            $sanitizedName = strtolower(
                iconv('UTF-8', 'ASCII//TRANSLIT', $name) // Remove accents
            );
            $sanitizedName = preg_replace('/[^a-z0-9]+/', '-', $sanitizedName); // Replace special chars with hyphens
            $sanitizedName = trim($sanitizedName, '-'); // Remove leading/trailing hyphens
            
            return 'https://www.' . substr($sanitizedName, 0, 30) . '.fr';
        }
        
        return null;
    }

    /**
     * Configure the model factory to create an active organization.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Configure the model factory to create an inactive organization.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the model factory to create an organization with a specific subscription type.
     */
    public function subscription(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_type' => $type,
            'subscription_ends_at' => $type === 'free' ? null : $this->faker->dateTimeBetween('now', '+1 year'),
        ]);
    }
} 