<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        Activity::create([
            'name' => 'Crop Harvesting',
            'description' => 'Tasks related to harvesting crops from the farm.',
            'icon' => '🌾',
            'created_by' => 1,
        ]);

        Activity::create([
            'name' => 'Animal Care',
            'description' => 'Tasks related to caring for farm animals.',
            'icon' => '🐄',
            'created_by' => 1,
        ]);

        Activity::create([
            'name' => 'Irrigation',
            'description' => 'Tasks related to watering and irrigating crops.',
            'icon' => '🪣',
            'created_by' => 1,
        ]);
    }
}
