<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Anggur', 'unit' => 'kg'],
            ['name' => 'Ayam', 'unit' => 'kg'],
            ['name' => 'Ayam Filet', 'unit' => 'kg'],
            ['name' => 'Bakso', 'unit' => 'kg'],
            ['name' => 'Bawang Bombay', 'unit' => 'kg'],
            ['name' => 'Bawang Merah', 'unit' => 'kg'],
            ['name' => 'Bawang Putih', 'unit' => 'kg'],
            ['name' => 'Beras', 'unit' => 'kg'],
            ['name' => 'Buncis', 'unit' => 'kg'],
            ['name' => 'Cabe Merah', 'unit' => 'kg'],
            ['name' => 'Cesim', 'unit' => 'kg'],
            ['name' => 'Daun Bawang', 'unit' => 'kg'],
            ['name' => 'Jagung', 'unit' => 'pcs'],
            ['name' => 'Jeruk', 'unit' => 'kg'],
            ['name' => 'Kacang Panjang', 'unit' => 'kg'],
            ['name' => 'Kangkung', 'unit' => 'ikat'],
            ['name' => 'Kecap Manis', 'unit' => 'pouch'],
            ['name' => 'Kembang Kol', 'unit' => 'kg'],
            ['name' => 'Kemiri', 'unit' => 'kg'],
            ['name' => 'Kentang Iris', 'unit' => 'kg'],
            ['name' => 'Kol', 'unit' => 'kg'],
            ['name' => 'Labu Siam', 'unit' => 'kg'],
            ['name' => 'Lada', 'unit' => 'kg'],
            ['name' => 'Melon', 'unit' => 'kg'],
            ['name' => 'Minyak Goreng', 'unit' => 'liter'],
            ['name' => 'Nugget', 'unit' => 'kg'],
            ['name' => 'Pakcoy', 'unit' => 'kg'],
            ['name' => 'Paneer', 'unit' => 'kg'],
            ['name' => 'Pepaya', 'unit' => 'kg'],
            ['name' => 'Putren', 'unit' => 'kg'],
            ['name' => 'Saori Saus Tiram', 'unit' => 'liter'],
            ['name' => 'Saus BBQ', 'unit' => 'liter'],
            ['name' => 'Saus Teriyaki', 'unit' => 'liter'],
            ['name' => 'Sawi Putih', 'unit' => 'kg'],
            ['name' => 'Seledri', 'unit' => 'ikat'],
            ['name' => 'Semangka', 'unit' => 'kg'],
            ['name' => 'Susu', 'unit' => 'pcs'],
            ['name' => 'Tahu', 'unit' => 'pcs'],
            ['name' => 'Tauge', 'unit' => 'kg'],
            ['name' => 'Telur Ayam', 'unit' => 'kg'],
            ['name' => 'Telur Puyuh', 'unit' => 'butir'],
            ['name' => 'Tempe', 'unit' => 'batang'],
            ['name' => 'Tepung Beras', 'unit' => 'kg'],
            ['name' => 'Tepung Serbaguna', 'unit' => 'kg'],
            ['name' => 'Tepung Tapioka', 'unit' => 'kg'],
            ['name' => 'Tepung Terigu', 'unit' => 'kg'],
            ['name' => 'Udang Medium', 'unit' => 'kg'],
            ['name' => 'Wortel', 'unit' => 'kg'],
            ['name' => 'Yakult', 'unit' => 'pak'],
        ];

        foreach ($ingredients as $item) {
            Ingredient::firstOrCreate(
                ['name' => $item['name']],
                ['unit' => $item['unit']]
            );
        }
    }
}
