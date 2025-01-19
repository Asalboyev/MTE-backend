<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'products_categories';

        // if (Schema::hasTable($table)) {
            DB::table($table)->insert([
                [
                    'title' => json_encode([
                        'ru' => 'спецтехника'
                    ]),
                    'in_main' => 1
                ],
                [
                    'title' => json_encode([
                        'ru' => 'запчасти'
                    ]),
                    'in_main' => 1
                ],
                [
                    'title' => json_encode([
                        'ru' => 'Разработки',
                        'en' => 'Developments'
                    ]),
                    'in_main' => 0
                ]
            ]);
        // }
    }
}
