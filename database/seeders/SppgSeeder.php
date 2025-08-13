<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SppgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sppgs = [
            ['name' => 'SheIsh Catering', 'district' => 'Slawi', 'regency' => 'Kab. Tegal', 'province' => 'Jawa Tengah'],
            ['name' => 'Tiga Putri Catering', 'district' => 'Maegasari', 'regency' => 'Kab. Tegal', 'province' => 'Jawa Tengah'],
            ['name' => 'Amelia Catering', 'district' => 'Tarub', 'regency' => 'Kab. Tegal', 'province' => 'Jawa Tengah'],
        ];
        foreach ($sppgs as $sppg) {
            DB::table('sppgs')->insert([
                'name' => $sppg['name'],
                'district' => $sppg['district'],
                'regency' => $sppg['regency'],
                'province' => $sppg['province'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
