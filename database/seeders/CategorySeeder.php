<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Define an array of category data
        $categories = [];

        // Generate 10 categories using Faker
        for ($i = 0; $i < 5; $i++) {
            $categories[] = [
                'name' => $faker->word, // Generate a random word for category name
                'sku' => 'cat-' . ($i + 1), // Generate SKU based on category index
                'image' => NULL // Generate a random image URL
            ];
        }

        // Insert categories into the database
        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
