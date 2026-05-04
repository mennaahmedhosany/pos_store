<?php

namespace Database\Seeders;

use App\Models\Cupsize;
use App\Models\Extra;
use App\Models\WaterType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (
            [
                ['name' => 'Small',   'volume' => '350ml', 'price' => 5.00,  'sort_order' => 1],
                ['name' => 'Medium',  'volume' => '500ml', 'price' => 8.00,  'sort_order' => 2],
                ['name' => 'Large',   'volume' => '750ml', 'price' => 12.00, 'sort_order' => 3],
                ['name' => 'X-Large', 'volume' => '1L',    'price' => 16.00, 'sort_order' => 4],
                ['name' => 'Gallon',  'volume' => '1.5L',  'price' => 22.00, 'sort_order' => 5],
                ['name' => 'Jumbo',   'volume' => '2L',    'price' => 28.00, 'sort_order' => 6],
            ] as $s
        ) {
            Cupsize::firstOrCreate(['name' => $s['name']], $s);
        }

        foreach (
            [
                ['name' => 'Still',     'description' => 'Purified flat',    'price' => 0.00, 'sort_order' => 1],
                ['name' => 'Sparkling', 'description' => 'Carbonated',        'price' => 3.00, 'sort_order' => 2],
                ['name' => 'Alkaline',  'description' => 'pH 9+',             'price' => 5.00, 'sort_order' => 3],
                ['name' => 'Mineral',   'description' => 'Natural mineral',   'price' => 4.00, 'sort_order' => 4],
                ['name' => 'Infused',   'description' => 'Fruit infused',     'price' => 6.00, 'sort_order' => 5],
                ['name' => 'Coconut',   'description' => 'Coconut water',     'price' => 7.00, 'sort_order' => 6],
            ] as $w
        ) {
            WaterType::firstOrCreate(['name' => $w['name']], $w);
        }

        foreach (
            [
                ['name' => 'Lemon slice',  'price' => 2.00, 'sort_order' => 1],
                ['name' => 'Fresh mint',   'price' => 2.00, 'sort_order' => 2],
                ['name' => 'Extra ice',    'price' => 1.00, 'sort_order' => 3],
                ['name' => 'Cucumber',     'price' => 3.00, 'sort_order' => 4],
                ['name' => 'Ginger shot',  'price' => 5.00, 'sort_order' => 5],
                ['name' => 'Chia seeds',   'price' => 4.00, 'sort_order' => 6],
                ['name' => 'Aloe vera',    'price' => 5.00, 'sort_order' => 7],
                ['name' => 'Collagen +',   'price' => 8.00, 'sort_order' => 8],
            ] as $e
        ) {
            Extra::firstOrCreate(['name' => $e['name']], $e);
        }
    }
}
