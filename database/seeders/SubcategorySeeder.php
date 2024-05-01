<?php

namespace Database\Seeders;

use App\Models\SubCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) { // Generate 20 fake subcategories
            SubCategories::create([
                'name' => $faker->word,
                'sku' => $faker->unique()->slug(2), // Generate a unique SKU
                'category_id' => rand(1, 10), // Assuming you have 10 categories
                'status' => $faker->boolean(70) ? 1 : 0, // 70% chance of being active (status = 1)
            ]);
        }
    }
}
