<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Énergie renouvelable',
            'Agriculture biologique',
            'Recyclage et déchets',
            'Conservation de la nature',
            'Écomobilité',
            'Habitat écologique',
            'Économie circulaire',
            'Éducation environnementale',
            'Biodiversité',
            'Eau et assainissement',
        ];

        $now = now();
        
        foreach ($categories as $category) {
            DB::table('project_categories')->insert([
                'name' => $category,
                'description' => 'Projets liés à ' . mb_strtolower($category),
                'created_at' => $now,
            ]);
        }
    }
}