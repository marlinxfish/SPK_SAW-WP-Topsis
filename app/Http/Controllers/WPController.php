<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiMatrix;
use Illuminate\Http\Request;

class WPController extends Controller
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

        // Hitung total bobot untuk normalisasi
        $totalBobot = $kriterias->sum('bobot');

        // Normalisasi bobot (Wj' = Wj / total bobot)
        $bobotTernormalisasi = [];
        foreach ($kriterias as $krit) {
            $bobotTernormalisasi[$krit->id] = $krit->bobot / $totalBobot;
        }

        // Hitung vektor S (perkalian kriteria dengan pangkat bobot)
        $vektorS = [];
        foreach ($alternatifs as $alt) {
            $vektorS[$alt->id] = 1; // Inisialisasi dengan 1 untuk perkalian
            
            foreach ($kriterias as $krit) {
                $xij = $matrix[$alt->id][$krit->id] ?? 0;
                
                // Jika nilai 0, ganti dengan 0.01 untuk menghindari error log(0)
                $xij = $xij == 0 ? 0.01 : $xij;
                
                if ($krit->sifat == 'benefit') {
                    $vektorS[$alt->id] *= pow($xij, $bobotTernormalisasi[$krit->id]);
                } else {
                    $vektorS[$alt->id] *= pow($xij, -$bobotTernormalisasi[$krit->id]);
                }
            }
        }

        // Hitung total vektor S
        $totalVektorS = array_sum($vektorS);

        // Hitung vektor V (normalisasi vektor S)
        $result = [];
        foreach ($alternatifs as $alt) {
            $vektorV = $vektorS[$alt->id] / $totalVektorS;
            
            $result[] = [
                'kode' => $alt->kode_alternatif,
                'nama' => $alt->nama_alternatif,
                'skor' => $vektorV,
                'rank' => 0
            ];
        }

        // Urutkan berdasarkan skor tertinggi
        usort($result, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Tambahkan peringkat
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
            $item['skor'] = round($item['skor'], 4);
            $prevScore = $item['skor'];
        }

        return view('wp.index', [
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'matrix' => $matrix,
            'bobotTernormalisasi' => $bobotTernormalisasi,
            'vektorS' => $vektorS,
            'result' => $result,
            'totalVektorS' => $totalVektorS
        ]);
    }
}
