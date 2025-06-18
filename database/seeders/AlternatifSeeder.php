<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Alternatif;
use Illuminate\Support\Facades\DB;

class AlternatifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alternatif::insert([
            ['kode_alternatif' => 'A1', 'nama_alternatif' => 'Akhmad Wahyudi'],
            ['kode_alternatif' => 'A2', 'nama_alternatif' => 'Akhmad Rosidi'],
            ['kode_alternatif' => 'A3', 'nama_alternatif' => 'Kaspul'],
            ['kode_alternatif' => 'A4', 'nama_alternatif' => 'Andi Hidayat'],
            ['kode_alternatif' => 'A5', 'nama_alternatif' => 'Dessy'],
        ]);
    }
}
