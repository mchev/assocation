<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Catégories principales
        $categories = [
            'Sonorisation' => [
                'Enceintes',
                'Amplificateurs',
                'Tables de mixage',
                'Microphones',
                'Câbles audio',
            ],
            'Éclairage' => [
                'Projecteurs',
                'Stroboscopes',
                'Lasers',
                'Câbles DMX',
                'Contrôleurs',
            ],
            'Scène' => [
                'Structures',
                'Rideaux',
                'Décors',
                'Accessoires',
            ],
            'Vidéo' => [
                'Écrans',
                'Projecteurs vidéo',
                'Caméras',
                'Câbles vidéo',
            ],
            'Informatique' => [
                'Ordinateurs',
                'Écrans',
                'Périphériques',
                'Réseau',
            ],
        ];

        foreach ($categories as $mainCategory => $subCategories) {
            // Créer la catégorie principale
            $parent = Category::create([
                'name' => $mainCategory,
                'slug' => Str::slug($mainCategory),
                'description' => "Équipements de {$mainCategory}",
                'order' => 0,
            ]);

            // Créer les sous-catégories
            foreach ($subCategories as $index => $subCategory) {
                Category::create([
                    'name' => $subCategory,
                    'slug' => Str::slug($mainCategory.'-'.$subCategory),
                    'description' => "Équipements de {$subCategory}",
                    'parent_id' => $parent->id,
                    'order' => $index,
                ]);
            }
        }

        // Ajouter quelques catégories aléatoires supplémentaires
        Category::factory()
            ->count(5)
            ->create();

        // Ajouter quelques sous-catégories aléatoires
        Category::factory()
            ->count(10)
            ->child(Category::inRandomOrder()->first())
            ->create();
    }
}
