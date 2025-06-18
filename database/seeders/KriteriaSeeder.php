<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kriteria::insert([
            ['kode_kriteria' => 'C1', 'nama_kriteria' => 'Sasaran Kinerja Individu', 'bobot' => 0.6, 'sifat' => 'benefit'],
            ['kode_kriteria' => 'C2', 'nama_kriteria' => 'Orientasi Pelayanan', 'bobot' => 0.08, 'sifat' => 'benefit'],
            ['kode_kriteria' => 'C3', 'nama_kriteria' => 'Integritas', 'bobot' => 0.08, 'sifat' => 'benefit'],
            ['kode_kriteria' => 'C4', 'nama_kriteria' => 'Komitmen', 'bobot' => 0.08, 'sifat' => 'benefit'],
            ['kode_kriteria' => 'C5', 'nama_kriteria' => 'Disiplin', 'bobot' => 0.08, 'sifat' => 'benefit'],
            ['kode_kriteria' => 'C6', 'nama_kriteria' => 'Kerja Sama', 'bobot' => 0.08, 'sifat' => 'benefit'],
        ]);
    }
}
