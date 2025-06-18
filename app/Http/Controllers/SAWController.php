<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiMatrix;
use Illuminate\Http\Request;

class SAWController extends Controller
{
    public function index()
    {
        // Ambil data kriteria dan alternatif
        $kriterias = Kriteria::orderBy('id')->get();
        $alternatifs = Alternatif::orderBy('id')->get();

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Data kriteria atau alternatif masih kosong. Silakan isi terlebih dahulu.');
        }

        // Ambil semua nilai dan simpan per alternatif dan per kriteria
        $matrix = [];
        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $krit) {
                $nilai = NilaiMatrix::where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                $matrix[$alt->id][$krit->id] = $nilai ? (float)$nilai->nilai : 0;
            }
        }

        // Hitung nilai maksimum dan minimum setiap kriteria
        $maxMin = [];
        foreach ($kriterias as $krit) {
            $column = array_column(array_map(fn($a) => $a[$krit->id] ?? 0, $matrix), null);
            $maxMin[$krit->id] = [
                'max' => !empty($column) ? max($column) : 0,
                'min' => !empty($column) ? min($column) : 0,
            ];
        }

        // Normalisasi matriks
        $normal = [];
        foreach ($kriterias as $krit) {
            foreach ($alternatifs as $alt) {
                $xij = $matrix[$alt->id][$krit->id] ?? 0;
                $max = $maxMin[$krit->id]['max'];
                $min = $maxMin[$krit->id]['min'];

                if ($krit->sifat == 'benefit') {
                    $rij = $max > 0 ? $xij / $max : 0;
                } else {
                    $rij = ($xij > 0 && $min > 0) ? $min / $xij : 0;
                }
                $normal[$alt->id][$krit->id] = round($rij, 4);
            }
        }

        // Hitung skor preferensi akhir (Vi)
        $result = [];
        foreach ($alternatifs as $alt) {
            $total = 0;
            foreach ($kriterias as $krit) {
                $total += $normal[$alt->id][$krit->id] * $krit->bobot;
            }
            $result[] = [
                'nama' => $alt->nama_alternatif,
                'kode' => $alt->kode_alternatif,
                'skor' => round($total, 4),
                'rank' => 0
            ];
        }

        // Konversi ke array untuk diurutkan
        $result = array_values($result);
        
        // Urutkan berdasarkan skor tertinggi
        usort($result, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Tambahkan peringkat dengan menangani skor yang sama
        $rank = 1;
        $prevScore = null;
        $sameRankCount = 1;
        
        foreach ($result as &$item) {
            if ($prevScore !== null && $item['skor'] < $prevScore) {
                $rank += $sameRankCount;
                $sameRankCount = 1;
            } elseif ($prevScore !== null && $item['skor'] == $prevScore) {
                $sameRankCount++;
            } else {
                $sameRankCount = 1;
            }
            
            $item['rank'] = $rank;
            $prevScore = $item['skor'];
        }

        return view('saw.index', [
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'matrix' => $matrix,
            'normal' => $normal,
            'result' => $result,
            'maxMin' => $maxMin
        ]);
    }
}
