<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Brand::create([
            'name' => 'ishonch',
            'user_id' => 1,
        ]);
        $brand = Brand::create([
            'name' => 'texnomart',
            'user_id' => 1,
        ]);
        $brand = Brand::create([
            'name' => 'mediapark',
            'user_id' => 1,
        ]);
    }
}
