<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'Twitter',
                'type' => 'twitter',
                'requirements' => json_encode([
                    'character_limit' => 280,
                    'image_required' => false,
                ]),
            ],
            [
                'name' => 'Instagram',
                'type' => 'instagram',
                'requirements' => json_encode([
                    'character_limit' => 2200,
                    'image_required' => true,
                ]),
            ],
            [
                'name' => 'LinkedIn',
                'type' => 'linkedin',
                'requirements' => json_encode([
                    'character_limit' => 3000,
                    'image_required' => false,
                ]),
            ],
            [
                'name' => 'Facebook',
                'type' => 'facebook',
                'requirements' => json_encode([
                    'character_limit' => 63206,
                    'image_required' => false,
                ]),
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::create($platform);
        }
    }
} 