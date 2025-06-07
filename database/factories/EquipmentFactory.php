<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Depot;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    protected $equipmentTypes = [
        // Sonorisation
        'Enceintes' => [
            'names' => [
                'Enceinte amplifiée 15"',
                'Caisson de basse 18"',
                'Enceinte retour de scène 12"',
                'Système line array compact',
                'Enceinte colonne active',
                'Système de sonorisation portable',
                'Kit son complet 2000W',
                'Système de diffusion 2.1'
            ],
            'specs' => [
                'puissance' => ['400W RMS', '600W RMS', '800W RMS', '1000W RMS', '2000W RMS'],
                'poids' => ['12 kg', '15 kg', '18 kg', '25 kg', '35 kg'],
                'connectique' => ['XLR, Jack', 'Speakon, XLR', 'XLR, RCA', 'Bluetooth, XLR'],
            ],
            'prices' => [30, 80]
        ],
        'Amplificateurs' => [
            'names' => [
                'Amplificateur de puissance 2x1000W',
                'Ampli sono 2x500W',
                'Amplificateur 4 canaux',
                'Module amplificateur classe D',
                'Amplificateur de monitoring',
                'Bloc de puissance 19"'
            ],
            'specs' => [
                'puissance' => ['2x500W', '2x1000W', '4x250W', '2x1500W'],
                'impédance' => ['4 ohms', '8 ohms'],
                'refroidissement' => ['Ventilation active', 'Passif', 'Ventilateurs thermorégulés'],
            ],
            'prices' => [40, 100]
        ],
        'Tables de mixage' => [
            'names' => [
                'Console de mixage numérique 16 voies',
                'Table de mixage analogique 12 voies',
                'Console DJ 2 voies',
                'Mixette 6 voies portable',
                'Console de mixage USB',
                'Table de mixage broadcast',
                'Console de mixage rackable'
            ],
            'specs' => [
                'voies' => ['6 voies', '12 voies', '16 voies', '24 voies', '32 voies'],
                'effets' => ['Reverb, Delay', '16 effets', '24 effets', '32 effets', 'Multi-effets'],
                'connexions' => ['USB, XLR', 'USB, MIDI, XLR', 'Dante, USB, XLR', 'AES/EBU'],
            ],
            'prices' => [35, 120]
        ],
        'Microphones' => [
            'names' => [
                'Micro chant dynamique',
                'Micro statique studio',
                'Kit micros batterie',
                'Micro sans fil main',
                'Micro cravate HF',
                'Micro d\'ambiance',
                'Micro instrument'
            ],
            'specs' => [
                'type' => ['Dynamique', 'Statique', 'Sans fil UHF', 'Sans fil VHF'],
                'directivité' => ['Cardioïde', 'Supercardioïde', 'Omnidirectionnel'],
                'connectique' => ['XLR', 'XLR/Jack', 'Mini-XLR'],
            ],
            'prices' => [15, 50]
        ],
        'Câbles audio' => [
            'names' => [
                'Kit câbles XLR',
                'Câbles patch',
                'Boîtier de direct',
                'Multipaire audio',
                'Câbles enceintes',
                'Kit de câblage DJ'
            ],
            'specs' => [
                'longueur' => ['5m', '10m', '15m', '20m', '30m'],
                'type' => ['XLR', 'Jack', 'Speakon', 'RCA'],
                'qualité' => ['Pro', 'Semi-pro', 'Standard'],
            ],
            'prices' => [5, 30]
        ],

        // Éclairage
        'Projecteurs' => [
            'names' => [
                'Projecteur LED RGBW',
                'PAR LED 18x10W',
                'Lyre Spot 200W',
                'Projecteur LED COB 100W',
                'Barre LED 8 sections',
                'Projecteur LED UV',
                'PAR à lampe',
                'Découpe LED'
            ],
            'specs' => [
                'puissance' => ['100W', '150W', '200W', '300W'],
                'modes' => ['DMX, Auto, Sound', 'DMX 8-16 canaux', 'DMX 16-24 canaux'],
                'angle' => ['25°', '45°', '60°', '15°-30°'],
            ],
            'prices' => [25, 70]
        ],
        'Stroboscopes' => [
            'names' => [
                'Stroboscope LED 1500W',
                'Stroboscope DMX',
                'Blinder 2 cellules',
                'Stroboscope LED RGBW'
            ],
            'specs' => [
                'puissance' => ['400W', '800W', '1500W'],
                'contrôle' => ['DMX', 'DMX/Auto', 'DMX/Sound'],
                'vitesse' => ['1-30Hz', '1-20Hz', '1-15Hz'],
            ],
            'prices' => [20, 60]
        ],
        'Lasers' => [
            'names' => [
                'Laser multipoint RGB',
                'Laser d\'animation',
                'Projecteur laser DMX',
                'Laser graphique'
            ],
            'specs' => [
                'puissance' => ['500mW', '1W', '2W', '3W'],
                'couleurs' => ['RGB', 'RGY', 'Full RGB'],
                'sécurité' => ['Classe 3B', 'Classe 4'],
            ],
            'prices' => [40, 120]
        ],
        'Contrôleurs' => [
            'names' => [
                'Console DMX 24/48',
                'Contrôleur LED WiFi',
                'Interface DMX USB',
                'Pupitre lumière tactile',
                'Console lumière PC',
                'Splitter DMX'
            ],
            'specs' => [
                'canaux' => ['24/48 canaux', '128 canaux', '512 canaux', '1024 canaux'],
                'mémoires' => ['12 scènes', '24 scènes', '36 scènes', '48 scènes'],
                'connexions' => ['DMX 3 broches', 'DMX 5 broches', 'USB, DMX', 'Art-Net'],
            ],
            'prices' => [30, 80]
        ],

        // Scène
        'Structures' => [
            'names' => [
                'Structure alu carrée 2m',
                'Pont lumière 3m',
                'Pied de levage 4m',
                'Structure scène 2x1m',
                'Praticable 2x1m réglable',
                'Tour de son 5m',
                'Support d\'enceinte'
            ],
            'specs' => [
                'charge' => ['50 kg', '80 kg', '100 kg', '150 kg'],
                'matériau' => ['Aluminium', 'Acier', 'Acier galvanisé'],
                'assemblage' => ['Manchon conique', 'Goupille', 'Clips rapides', 'Boulonnerie'],
            ],
            'prices' => [20, 60]
        ],
        'Rideaux' => [
            'names' => [
                'Pendrillon noir 3m',
                'Rideau de fond noir',
                'Rideau occultant',
                'Backdrop scène',
                'Jupe de scène'
            ],
            'specs' => [
                'hauteur' => ['2m', '3m', '4m', '6m'],
                'largeur' => ['3m', '4m', '6m', '8m'],
                'matière' => ['Velours', 'Molleton', 'Trevira CS'],
            ],
            'prices' => [15, 45]
        ],
        'Décors' => [
            'names' => [
                'Fond vert chromakey',
                'Toile de projection',
                'Moquette scène',
                'Tapis de danse',
                'Cyclorama blanc'
            ],
            'specs' => [
                'dimensions' => ['3x3m', '4x3m', '6x3m', '8x4m'],
                'matériau' => ['PVC', 'Tissus', 'Vinyle', 'Coton'],
                'classement_feu' => ['M1', 'M2', 'M3'],
            ],
            'prices' => [25, 75]
        ],

        // Vidéo
        'Projecteurs vidéo' => [
            'names' => [
                'Vidéoprojecteur 5000 lumens',
                'Projecteur HD 1080p',
                'Vidéoprojecteur courte focale',
                'Projecteur 4K compact',
                'Vidéoprojecteur laser',
                'Projecteur événementiel'
            ],
            'specs' => [
                'luminosité' => ['3000 lumens', '4000 lumens', '5000 lumens', '7000 lumens'],
                'résolution' => ['Full HD', '4K', 'WUXGA'],
                'contraste' => ['2000:1', '3000:1', '5000:1', '10000:1'],
            ],
            'prices' => [50, 150]
        ],
        'Écrans' => [
            'names' => [
                'Écran sur pied 16/9',
                'Écran électrique',
                'Écran de rétroprojection',
                'Moniteur de régie',
                'Écran LED modulaire'
            ],
            'specs' => [
                'dimensions' => ['180x120cm', '200x150cm', '300x200cm', '400x300cm'],
                'format' => ['4:3', '16:9', '16:10'],
                'type' => ['Cadre alu', 'Portable', 'Motorisé'],
            ],
            'prices' => [30, 100]
        ],
        'Caméras' => [
            'names' => [
                'Caméra PTZ',
                'Caméscope pro',
                'Caméra streaming',
                'Kit multicam',
                'Caméra sport'
            ],
            'specs' => [
                'résolution' => ['Full HD', '4K', '6K'],
                'capteur' => ['1/2.8"', '1"', 'Super 35'],
                'sorties' => ['SDI', 'HDMI', 'NDI'],
            ],
            'prices' => [45, 150]
        ],
        'Câbles vidéo' => [
            'names' => [
                'Câble HDMI pro',
                'Câble SDI',
                'Convertisseur SDI/HDMI',
                'Extendeur HDMI',
                'Câble fibre optique'
            ],
            'specs' => [
                'longueur' => ['5m', '10m', '15m', '20m', '30m'],
                'type' => ['HDMI', 'SDI', 'DisplayPort', 'DVI'],
                'qualité' => ['4K', 'HD', 'SD'],
            ],
            'prices' => [10, 40]
        ],

        // Informatique
        'Ordinateurs' => [
            'names' => [
                'PC régie son',
                'Mac régie vidéo',
                'PC contrôle lumière',
                'Ordinateur streaming',
                'Station de travail audio'
            ],
            'specs' => [
                'processeur' => ['i7', 'i9', 'Xeon'],
                'ram' => ['16 Go', '32 Go', '64 Go'],
                'stockage' => ['SSD 500Go', 'SSD 1To', 'SSD 2To'],
            ],
            'prices' => [60, 200]
        ],
        'Périphériques' => [
            'names' => [
                'Interface audio USB',
                'Contrôleur MIDI',
                'Surface de contrôle',
                'Carte son externe',
                'Clavier maître'
            ],
            'specs' => [
                'connexion' => ['USB', 'Thunderbolt', 'FireWire'],
                'compatibilité' => ['PC/Mac', 'iOS', 'Android'],
                'alimentation' => ['USB', 'Secteur', 'PoE'],
            ],
            'prices' => [20, 80]
        ],
        'Réseau' => [
            'names' => [
                'Switch réseau manageable',
                'Routeur WiFi pro',
                'Point d\'accès WiFi',
                'Switch DMX Ethernet',
                'Node Art-Net'
            ],
            'specs' => [
                'ports' => ['8 ports', '16 ports', '24 ports'],
                'débit' => ['100 Mbps', '1 Gbps', '10 Gbps'],
                'protocoles' => ['TCP/IP', 'Art-Net', 'sACN'],
            ],
            'prices' => [25, 90]
        ]
    ];

    public function definition()
    {
        $category = Category::inRandomOrder()->first();
        $equipmentType = $this->getEquipmentTypeForCategory($category->name);
        
        if (!$equipmentType) {
            // Si la catégorie n'est pas dans notre liste, on prend un type au hasard
            $randomType = array_rand($this->equipmentTypes);
            $equipmentType = $this->equipmentTypes[$randomType];
        }

        $name = $this->faker->randomElement($equipmentType['names']);
        $specs = [];
        foreach ($equipmentType['specs'] as $key => $values) {
            $specs[$key] = $this->faker->randomElement($values);
        }
        $rentalPrice = $this->faker->numberBetween($equipmentType['prices'][0], $equipmentType['prices'][1]);

        // Si l'organization_id est défini, on prend un dépôt de cette organisation
        $depot = null;
        if (isset($this->organization_id)) {
            $depot = Depot::where('organization_id', $this->organization_id)->inRandomOrder()->first();
        }

        return [
            'name' => $name,
            'description' => $this->generateDescription($name, $specs),
            'category_id' => $category->id,
            'condition' => $this->faker->randomElement([
                'Neuf',
                'Très bon état',
                'Bon état',
                'État moyen',
                'À réviser'
            ]),
            'is_available' => $this->faker->boolean(80),
            'is_rentable' => true,
            'requires_deposit' => $this->faker->boolean(70),
            'rental_price' => $rentalPrice,
            'deposit_amount' => $rentalPrice * 5,
            'specifications' => $specs,
            'last_maintenance_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'next_maintenance_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'depot_id' => $depot ? $depot->id : null,
        ];
    }

    protected function getEquipmentTypeForCategory($categoryName)
    {
        return $this->equipmentTypes[$categoryName] ?? null;
    }

    protected function generateDescription($name, $specs)
    {
        $description = $name . " - ";
        foreach ($specs as $key => $value) {
            $description .= ucfirst($key) . " : " . $value . ". ";
        }
        $description .= "\n\nÉquipement professionnel idéal pour vos événements. ";
        $description .= $this->faker->randomElement([
            "Parfait pour les associations et particuliers. ",
            "Adapté aux petits et moyens événements. ",
            "Convient pour tout type de manifestation. ",
            "Solution idéale pour vos besoins techniques. "
        ]);
        $description .= $this->faker->randomElement([
            "Livré avec tous les câbles nécessaires. ",
            "Installation simple et rapide. ",
            "Formation rapide possible sur place. ",
            "Notice d'utilisation fournie. "
        ]);

        return $description;
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Equipment $equipment) {
            // Si l'équipement n'a pas de dépôt mais a une organisation
            if (!$equipment->depot_id && $equipment->organization_id) {
                // On cherche un dépôt de l'organisation ou on en crée un
                $depot = Depot::where('organization_id', $equipment->organization_id)->inRandomOrder()->first();
                if (!$depot) {
                    $depot = Depot::factory()->create(['organization_id' => $equipment->organization_id]);
                }
                $equipment->depot_id = $depot->id;
                $equipment->save();
            }
            // Si l'équipement a un dépôt mais pas d'organisation
            elseif ($equipment->depot_id && !$equipment->organization_id) {
                $depot = $equipment->depot;
                if ($depot) {
                    $equipment->organization_id = $depot->organization_id;
                    $equipment->save();
                }
            }
        });
    }
} 