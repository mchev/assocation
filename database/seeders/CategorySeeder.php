<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed event equipment categories.
     */
    public function run(): void
    {
        $categories = [
            // Son
            [
                'name' => 'Sonorisation',
                'description' => 'Équipements de sonorisation professionnelle',
                'icon' => 'speaker-wave',
                'children' => [
                    [
                        'name' => 'Enceintes',
                        'description' => 'Enceintes amplifiées et passives',
                        'icon' => 'speaker',
                    ],
                    [
                        'name' => 'Consoles de mixage',
                        'description' => 'Tables de mixage numériques et analogiques',
                        'icon' => 'sliders',
                    ],
                    [
                        'name' => 'Microphones',
                        'description' => 'Microphones filaires et sans fil',
                        'icon' => 'mic',
                    ],
                    [
                        'name' => 'Processeurs et effets',
                        'description' => 'Processeurs de signal et effets audio',
                        'icon' => 'wave-sine',
                    ],
                    [
                        'name' => 'Amplificateurs',
                        'description' => 'Amplificateurs de puissance',
                        'icon' => 'activity',
                    ],
                    [
                        'name' => 'Câblage audio',
                        'description' => 'Câbles XLR, Jack, multiprises audio',
                        'icon' => 'cable',
                    ],
                    [
                        'name' => 'Monitoring',
                        'description' => 'Retours de scène, in-ear monitoring',
                        'icon' => 'headphones',
                    ],
                    [
                        'name' => 'DI Box et Splitters',
                        'description' => 'Boîtiers de direct et splitters audio',
                        'icon' => 'route',
                    ],
                    [
                        'name' => 'Autre sonorisation',
                        'description' => 'Autres équipements de sonorisation',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Lumière
            [
                'name' => 'Éclairage',
                'description' => 'Systèmes d\'éclairage pour événements',
                'icon' => 'lamp-desk',
                'children' => [
                    [
                        'name' => 'Projecteurs LED',
                        'description' => 'Projecteurs LED PAR, wash et beam',
                        'icon' => 'spotlight',
                    ],
                    [
                        'name' => 'Lyres',
                        'description' => 'Lyres beam, spot et wash',
                        'icon' => 'lamp',
                    ],
                    [
                        'name' => 'Gradateurs et contrôleurs',
                        'description' => 'Gradateurs et consoles DMX',
                        'icon' => 'sliders-horizontal',
                    ],
                    [
                        'name' => 'Effets lumineux',
                        'description' => 'Stroboscopes, lasers et effets spéciaux',
                        'icon' => 'sparkles',
                    ],
                    [
                        'name' => 'Projecteurs traditionnels',
                        'description' => 'Projecteurs à lampe, découpes, PC',
                        'icon' => 'sun',
                    ],
                    [
                        'name' => 'Contrôle DMX',
                        'description' => 'Interfaces DMX, splitters, câblage',
                        'icon' => 'gamepad-2',
                    ],
                    [
                        'name' => 'Éclairage architectural',
                        'description' => 'Projecteurs d\'ambiance et décoration',
                        'icon' => 'building',
                    ],
                    [
                        'name' => 'Machine à fumée',
                        'description' => 'Machines à fumée, brouillard et CO2',
                        'icon' => 'cloud',
                    ],
                    [
                        'name' => 'Autre éclairage',
                        'description' => 'Autres équipements d\'éclairage',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Vidéo
            [
                'name' => 'Vidéo',
                'description' => 'Équipements de projection et diffusion vidéo',
                'icon' => 'video',
                'children' => [
                    [
                        'name' => 'Écrans LED',
                        'description' => 'Murs LED et écrans haute résolution',
                        'icon' => 'tv',
                    ],
                    [
                        'name' => 'Vidéoprojecteurs',
                        'description' => 'Projecteurs haute luminosité',
                        'icon' => 'projector',
                    ],
                    [
                        'name' => 'Mélangeurs vidéo',
                        'description' => 'Mélangeurs et switchers vidéo',
                        'icon' => 'git-merge',
                    ],
                    [
                        'name' => 'Écrans et toiles',
                        'description' => 'Écrans de projection et toiles',
                        'icon' => 'monitor',
                    ],
                    [
                        'name' => 'Caméras',
                        'description' => 'Caméras broadcast et accessoires',
                        'icon' => 'camera',
                    ],
                    [
                        'name' => 'Convertisseurs',
                        'description' => 'Convertisseurs et scalers vidéo',
                        'icon' => 'replace',
                    ],
                    [
                        'name' => 'Streaming',
                        'description' => 'Équipements de streaming et captation',
                        'icon' => 'wifi',
                    ],
                    [
                        'name' => 'Autre vidéo',
                        'description' => 'Autres équipements vidéo',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Structure
            [
                'name' => 'Structure',
                'description' => 'Structures scéniques et supports',
                'icon' => 'frame',
                'children' => [
                    [
                        'name' => 'Trusses',
                        'description' => 'Structures aluminium et accessoires',
                        'icon' => 'square-stack',
                    ],
                    [
                        'name' => 'Scènes',
                        'description' => 'Praticables et scènes modulaires',
                        'icon' => 'stage',
                    ],
                    [
                        'name' => 'Pieds et supports',
                        'description' => 'Pieds d\'enceintes et supports d\'éclairage',
                        'icon' => 'chevrons-up',
                    ],
                    [
                        'name' => 'Ponts et moteurs',
                        'description' => 'Palans, moteurs et accessoires',
                        'icon' => 'arrow-up-down',
                    ],
                    [
                        'name' => 'Barrières',
                        'description' => 'Barrières de sécurité et crash',
                        'icon' => 'separator-horizontal',
                    ],
                    [
                        'name' => 'Podiums',
                        'description' => 'Podiums et tribunes',
                        'icon' => 'stairs',
                    ],
                    [
                        'name' => 'Autre structure',
                        'description' => 'Autres équipements de structure',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Énergie
            [
                'name' => 'Énergie',
                'description' => 'Distribution électrique et alimentation',
                'icon' => 'zap',
                'children' => [
                    [
                        'name' => 'Groupes électrogènes',
                        'description' => 'Générateurs et groupes électrogènes',
                        'icon' => 'power',
                    ],
                    [
                        'name' => 'Distribution électrique',
                        'description' => 'Armoires et câbles électriques',
                        'icon' => 'plug',
                    ],
                    [
                        'name' => 'Transformateurs',
                        'description' => 'Transformateurs et convertisseurs',
                        'icon' => 'battery-charging',
                    ],
                    [
                        'name' => 'Rallonges',
                        'description' => 'Rallonges et enrouleurs',
                        'icon' => 'cable-car',
                    ],
                    [
                        'name' => 'Batteries',
                        'description' => 'Batteries et chargeurs',
                        'icon' => 'battery',
                    ],
                    [
                        'name' => 'Autre énergie',
                        'description' => 'Autres équipements électriques',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Transport
            [
                'name' => 'Transport',
                'description' => 'Équipements de transport et stockage',
                'icon' => 'truck',
                'children' => [
                    [
                        'name' => 'Flight cases',
                        'description' => 'Caisses de transport sur mesure',
                        'icon' => 'box',
                    ],
                    [
                        'name' => 'Chariots et transpalettes',
                        'description' => 'Équipements de manutention',
                        'icon' => 'forklift',
                    ],
                    [
                        'name' => 'Sangles et élingues',
                        'description' => 'Matériel d\'arrimage',
                        'icon' => 'link',
                    ],
                    [
                        'name' => 'Housses',
                        'description' => 'Housses de protection',
                        'icon' => 'shirt',
                    ],
                    [
                        'name' => 'Caisses et valises',
                        'description' => 'Caisses de rangement standards',
                        'icon' => 'package',
                    ],
                    [
                        'name' => 'Autre transport',
                        'description' => 'Autres équipements de transport',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
            // Mobilier
            [
                'name' => 'Mobilier',
                'description' => 'Mobilier événementiel',
                'icon' => 'armchair',
                'children' => [
                    [
                        'name' => 'Tables',
                        'description' => 'Tables pliantes et modulaires',
                        'icon' => 'table-2',
                    ],
                    [
                        'name' => 'Chaises',
                        'description' => 'Chaises et bancs pliants',
                        'icon' => 'chair',
                    ],
                    [
                        'name' => 'Décoration',
                        'description' => 'Éléments de décoration événementielle',
                        'icon' => 'flower-2',
                    ],
                    [
                        'name' => 'Stands',
                        'description' => 'Mobilier et structures de stand',
                        'icon' => 'store',
                    ],
                    [
                        'name' => 'Tentes et chapiteaux',
                        'description' => 'Structures de couverture temporaire',
                        'icon' => 'tent',
                    ],
                    [
                        'name' => 'Accessoires',
                        'description' => 'Accessoires et consommables',
                        'icon' => 'puzzle',
                    ],
                    [
                        'name' => 'Autre mobilier',
                        'description' => 'Autres équipements de mobilier',
                        'icon' => 'more-horizontal',
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::create($childData);
            }
        }
    }
}
