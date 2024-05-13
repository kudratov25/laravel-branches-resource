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
            'name' => 'cts',
            'domain' => 'https://ctsbackend.uz',
            'user_id' => 1,
            'status' => 1,
        ]);
        $brand = Brand::create([
            'name' => 'oilabuilding',
            'domain' => 'https://oilabackend.uz',
            'user_id' => 1,
            'status' => 1,
        ]);

    }
}
