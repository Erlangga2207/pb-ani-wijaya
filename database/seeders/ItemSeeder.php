<?php

namespace Database\Seeders;

use App\Enums\ItemType;
use App\Models\Item;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code' => 'GBH', 'name' => 'Gabah Ketan', 'type' => ItemType::Raw],
            ['code' => 'BKT', 'name' => 'Beras Ketan Putih', 'type' => ItemType::Finished, 'product_id' => Product::query()->value('id')],
            ['code' => 'MNR', 'name' => 'Menir', 'type' => ItemType::Byproduct],
            ['code' => 'DDK', 'name' => 'Dedak / Bekatul', 'type' => ItemType::Byproduct],
            ['code' => 'SKM', 'name' => 'Sekam', 'type' => ItemType::Byproduct],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['code' => $item['code']], $item);
        }
    }
}
