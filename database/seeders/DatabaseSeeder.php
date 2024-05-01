<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Update or create a user
        User::updateOrCreate(
            ['email' => 'kafi@gmail.com'], // Search criteria
            [
                'name' => 'Kafi Developer',
                'email' => 'kafi@gmail.com',
                'password' => Hash::make('123456'),
            ]
        );

        // Call other seeders
        $this->call(CategorySeeder::class);
        $this->call(SubcategorySeeder::class);
    }
}
