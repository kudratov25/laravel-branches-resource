<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement(
            "INSERT INTO `regions` (`id`, `name_uz`, `name_oz`, `name_ru`) VALUES
            (1, 'Qoraqalpog‘iston Respublikasi', 'Қорақалпоғистон Республикаси', 'Республика Каракалпакстан'),
            (2, 'Andijon viloyati', 'Андижон вилояти', 'Андижанская область'),
            (3, 'Buxoro viloyati', 'Бухоро вилояти', 'Бухарская область'),
            (4, 'Jizzax viloyati', 'Жиззах вилояти', 'Джизакская область'),
            (5, 'Qashqadaryo viloyati', 'Қашқадарё вилояти', 'Кашкадарьинская область'),
            (6, 'Navoiy viloyati', 'Навоий вилояти', 'Навоийская область'),
            (7, 'Namangan viloyati', 'Наманган вилояти', 'Наманганская область'),
            (8, 'Samarqand viloyati', 'Самарқанд вилояти', 'Самаркандская область'),
            (9, 'Surxandaryo viloyati', 'Сурхандарё вилояти', 'Сурхандарьинская область'),
            (10, 'Sirdaryo viloyati', 'Сирдарё вилояти', 'Сырдарьинская область'),
            (11, 'Toshkent viloyati', 'Тошкент вилояти', 'Ташкентская область'),
            (12, 'Farg‘ona viloyati', 'Фарғона вилояти', 'Ферганская область'),
            (13, 'Xorazm viloyati', 'Хоразм вилояти', 'Хорезмская область'),
            (14, 'Toshkent shahri', 'Тошкент шаҳри', 'Город Ташкент');"
        );
    }
}
