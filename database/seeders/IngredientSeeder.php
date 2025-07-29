<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Anggur', 'category' => 'buah', 'unit' => 'kg'],
            ['name' => 'Jeruk', 'category' => 'buah', 'unit' => 'kg'],
            ['name' => 'Melon', 'category' => 'buah', 'unit' => 'kg'],
            ['name' => 'Pepaya', 'category' => 'buah', 'unit' => 'kg'],
            ['name' => 'Semangka', 'category' => 'buah', 'unit' => 'kg'],
            ['name' => 'Kecap Manis', 'category' => 'bumbu', 'unit' => 'Pouch'],
            ['name' => 'Kemiri', 'category' => 'bumbu', 'unit' => 'Kg'],
            ['name' => 'Lada', 'category' => 'bumbu', 'unit' => 'Kg'],
            ['name' => 'Saori Saus Tiram', 'category' => 'bumbu', 'unit' => 'Liter'],
            ['name' => 'Saus BBQ', 'category' => 'bumbu', 'unit' => 'Liter'],
            ['name' => 'Saus Teriyaki', 'category' => 'bumbu', 'unit' => 'Liter'],
            ['name' => 'gula pasir', 'category' => 'bumbu', 'unit' => 'kg'],
            ['name' => 'garam', 'category' => 'bumbu', 'unit' => 'Pack'],
            ['name' => 'Penyedap Rasa', 'category' => 'bumbu', 'unit' => 'Pack'],
            ['name' => 'Saus BBQ', 'category' => 'bumbu', 'unit' => 'liter'],
            ['name' => 'Ayam', 'category' => 'daging', 'unit' => 'kg'],
            ['name' => 'Ayam Filet', 'category' => 'daging', 'unit' => 'kg'],
            ['name' => 'Udang Medium', 'category' => 'daging', 'unit' => 'kg'],
            ['name' => 'Bakso', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Nugget', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Tahu', 'category' => 'olahan', 'unit' => 'Pcs'],
            ['name' => 'Tempe', 'category' => 'olahan', 'unit' => 'Batang'],
            ['name' => 'Tepung Beras', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Tepung Serbaguna', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Tepung Tapioka', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Tepung terigu', 'category' => 'olahan', 'unit' => 'Kg'],
            ['name' => 'Bawang Bombay', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Bawang Merah', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Bawang Putih', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Buncis', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Cabe Merah', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Cesim', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Daun Bawang', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Jagung', 'category' => 'sayuran', 'unit' => 'Pcs'],
            ['name' => 'Kacang Panjang', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Kangkung', 'category' => 'sayuran', 'unit' => 'Ikat'],
            ['name' => 'Kembang Kol', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Kentang Iris', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Kol', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Labu Siam', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Pakcoy', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Putren', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Sawi Putih', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Tauge', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Wortel', 'category' => 'sayuran', 'unit' => 'Kg'],
            ['name' => 'Sledri', 'category' => 'sayuran', 'unit' => 'Ikat'],
            ['name' => 'Beras', 'category' => 'sereal', 'unit' => 'kg'],
            ['name' => 'Paneer', 'category' => 'protein', 'unit' => 'Kg'],
            ['name' => 'Susu', 'category' => 'protein', 'unit' => 'Pcs'],
            ['name' => 'Telur Ayam', 'category' => 'protein', 'unit' => 'Kg'],
            ['name' => 'Telur Puyuh', 'category' => 'protein', 'unit' => 'butir'],
            ['name' => 'Yakult', 'category' => 'protein', 'unit' => 'pack'],
            ['name' => 'minyak goreng', 'category' => 'minyak', 'unit' => 'liter'],
        ];

        foreach ($ingredients as $ingredient) {
            DB::table('ingredients')->insert([
                'name' => $ingredient['name'],
                'category' => $ingredient['category'],
                'unit' => $ingredient['unit'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
