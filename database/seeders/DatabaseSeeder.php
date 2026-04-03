<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\LabelFrequencySeeder::class,
            \Database\Seeders\LabelMealRelationSeeder::class,
            \Database\Seeders\LabelDosageSeeder::class,
            \Database\Seeders\LabelTimeSeeder::class,
            \Database\Seeders\LabelAdviceSeeder::class,
        ]);
    }
}
