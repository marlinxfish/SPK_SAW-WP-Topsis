<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiMatrix;
use Illuminate\Http\Request;

class TOPSISController extends Controller
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

        // 1. Matriks Keputusan (X)
        $matriksKeputusan = [];
        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $krit) {
                $nilai = NilaiMatrix::where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                $matriksKeputusan[$alt->id][$krit->id] = $nilai ? (float)$nilai->nilai : 0;
            }
        }

        // 2. Normalisasi Matriks (R)
        $matriksNormalisasi = [];
        $pangkatJumlah = [];
        
        // Hitung jumlah kuadrat tiap kriteria
        foreach ($kriterias as $krit) {
            $jumlahKuadrat = 0;
            foreach ($alternatifs as $alt) {
                $nilai = $matriksKeputusan[$alt->id][$krit->id];
                $jumlahKuadrat += pow($nilai, 2);
            }
            $pangkatJumlah[$krit->id] = sqrt($jumlahKuadrat);
        }

        // Hitung matriks ternormalisasi (R)
        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $krit) {
                $nilai = $matriksKeputusan[$alt->id][$krit->id];
                $matriksNormalisasi[$alt->id][$krit->id] = $pangkatJumlah[$krit->id] != 0 
                    ? $nilai / $pangkatJumlah[$krit->id] 
                    : 0;
            }
        }

        // 3. Matriks Ternormalisasi Terbobot (Y)
        $matriksTerbobot = [];
        $totalBobot = $kriterias->sum('bobot');
        
        foreach ($alternatifs as $alt) {
            foreach ($kriterias as $krit) {
                $bobotTernormalisasi = $krit->bobot / $totalBobot;
                $matriksTerbobot[$alt->id][$krit->id] = $matriksNormalisasi[$alt->id][$krit->id] * $bobotTernormalisasi;
            }
        }

        // 4. Solusi Ideal Positif (A+) dan Negatif (A-)
        $solusiIdealPositif = [];
        $solusiIdealNegatif = [];
        
        foreach ($kriterias as $krit) {
            $nilaiKriteria = [];
            foreach ($alternatifs as $alt) {
                $nilaiKriteria[] = $matriksTerbobot[$alt->id][$krit->id];
            }
            
            if ($krit->sifat == 'benefit') {
                $solusiIdealPositif[$krit->id] = max($nilaiKriteria);
                $solusiIdealNegatif[$krit->id] = min($nilaiKriteria);
            } else {
                $solusiIdealPositif[$krit->id] = min($nilaiKriteria);
                $solusiIdealNegatif[$krit->id] = max($nilaiKriteria);
            }
        }

        // 5. Jarak ke Solusi Ideal Positif (D+) dan Negatif (D-)
        $jarakPositif = [];
        $jarakNegatif = [];
        
        foreach ($alternatifs as $alt) {
            $totalPositif = 0;
            $totalNegatif = 0;
            
            foreach ($kriterias as $krit) {
                $totalPositif += pow($matriksTerbobot[$alt->id][$krit->id] - $solusiIdealPositif[$krit->id], 2);
                $totalNegatif += pow($matriksTerbobot[$alt->id][$krit->id] - $solusiIdealNegatif[$krit->id], 2);
            }
            
            $jarakPositif[$alt->id] = sqrt($totalPositif);
            $jarakNegatif[$alt->id] = sqrt($totalNegatif);
        }

        // 6. Nilai Preferensi (V)
        $nilaiPreferensi = [];
        foreach ($alternatifs as $alt) {
            $nilaiPreferensi[$alt->id] = $jarakNegatif[$alt->id] / ($jarakPositif[$alt->id] + $jarakNegatif[$alt->id]);
        }

        // 7. Perangkingan
        $result = [];
        foreach ($alternatifs as $alt) {
            $result[] = [
                'kode' => $alt->kode_alternatif,
                'nama' => $alt->nama_alternatif,
                'skor' => $nilaiPreferensi[$alt->id],
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

        return view('topsis.index', [
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'matriksKeputusan' => $matriksKeputusan,
            'matriksNormalisasi' => $matriksNormalisasi,
            'matriksTerbobot' => $matriksTerbobot,
            'solusiIdealPositif' => $solusiIdealPositif,
            'solusiIdealNegatif' => $solusiIdealNegatif,
            'jarakPositif' => $jarakPositif,
            'jarakNegatif' => $jarakNegatif,
            'result' => $result
        ]);
    }
}
