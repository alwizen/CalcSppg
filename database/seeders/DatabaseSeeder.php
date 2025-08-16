<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            IngredientSeeder::class,
            SppgSeeder::class
        ]);

        User::factory()->create([
            'name' => 'Admin RBJ',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Mayli',
            'email' => 'mayli@sheish.com',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Tika',
            'email' => 'tika@tigaputri.com',
            'password' => bcrypt('password'),
        ]);
    }
}
