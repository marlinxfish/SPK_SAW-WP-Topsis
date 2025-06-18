<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NilaiMatrix;
use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;

class NilaiMatrixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // A1
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C1', 'nilai' => 80],
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C2', 'nilai' => 80],
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C3', 'nilai' => 81],
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C4', 'nilai' => 78],
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C5', 'nilai' => 79],
            ['alternatif_kode' => 'A1', 'kriteria_kode' => 'C6', 'nilai' => 80],

            // A2
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C1', 'nilai' => 81],
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C2', 'nilai' => 80],
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C3', 'nilai' => 79],
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C4', 'nilai' => 80],
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C5', 'nilai' => 80],
            ['alternatif_kode' => 'A2', 'kriteria_kode' => 'C6', 'nilai' => 79],

            // A3
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C1', 'nilai' => 80],
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C2', 'nilai' => 79],
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C3', 'nilai' => 78],
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C4', 'nilai' => 78],
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C5', 'nilai' => 80],
            ['alternatif_kode' => 'A3', 'kriteria_kode' => 'C6', 'nilai' => 81],

            // A4
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C1', 'nilai' => 79],
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C2', 'nilai' => 81],
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C3', 'nilai' => 77],
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C4', 'nilai' => 80],
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C5', 'nilai' => 80],
            ['alternatif_kode' => 'A4', 'kriteria_kode' => 'C6', 'nilai' => 80],

            // A5
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C1', 'nilai' => 80],
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C2', 'nilai' => 80],
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C3', 'nilai' => 81],
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C4', 'nilai' => 80],
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C5', 'nilai' => 81],
            ['alternatif_kode' => 'A5', 'kriteria_kode' => 'C6', 'nilai' => 80],
        ];

        foreach ($data as $item) {
            $alternatif = Alternatif::where('kode_alternatif', $item['alternatif_kode'])->first();
            $kriteria = Kriteria::where('kode_kriteria', $item['kriteria_kode'])->first();

            if ($alternatif && $kriteria) {
                NilaiMatrix::create([
                    'alternatif_id' => $alternatif->id,
                    'kriteria_id' => $kriteria->id,
                    'nilai' => $item['nilai'],
                ]);
            }
        }
    }
}
