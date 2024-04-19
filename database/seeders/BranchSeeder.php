<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Branch::create([
            'name' => 'ishonch filial',
            'brand_id' => 1,
            'district_id' => 15
        ]);
        $brand = Branch::create([
            'name' => 'texnomart filial',
            'brand_id' => 2,
            'district_id' => 25
        ]);
        $brand = Branch::create([
            'name' => 'mediapark filial',
            'brand_id' => 3,
            'district_id' => 30
        ]);
    }
}
