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
            'inn' => '123456789',
            'domain' => 'https://ctsbackend.uz',
            'user_id' => 1,
            'status' => 1,
        ]);
        $brand = Brand::create([
            'name' => 'oilabuilding',
            'inn' => '112233445',
            'domain' => 'https://oilabackend.uz',
            'user_id' => 1,
            'status' => 1,
        ]);

    }
}
