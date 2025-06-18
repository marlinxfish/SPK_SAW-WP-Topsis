<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerbandinganController extends Controller
{
    public function index()
    {
        try {
            // Dapatkan hasil dari setiap metode
            $sawController = new \App\Http\Controllers\SAWController();
            $wpController = new \App\Http\Controllers\WPController();
            $topsisController = new \App\Http\Controllers\TOPSISController();
            
            // Panggil method index dari setiap controller dan dapatkan data view-nya
            $sawView = $sawController->index();
            $wpView = $wpController->index();
            $topsisView = $topsisController->index();
            
            // Jika ada redirect (misalnya karena data kosong), ikuti redirect-nya
            if (method_exists($sawView, 'getTargetUrl')) {
                return $sawView;
            }
            if (method_exists($wpView, 'getTargetUrl')) {
                return $wpView;
            }
            if (method_exists($topsisView, 'getTargetUrl')) {
                return $topsisView;
            }
            
            // Dapatkan data dari view
            $sawData = $sawView->getData();
            $wpData = $wpView->getData();
            $topsisData = $topsisView->getData();
            
            // Log data yang diterima untuk debugging
            \Log::info('Data SAW:', ['saw' => $sawData['result'] ?? []]);
            \Log::info('Data WP:', ['wp' => $wpData['result'] ?? []]);
            \Log::info('Data TOPSIS:', ['topsis' => $topsisData['result'] ?? []]);
            
            // Debug: Tampilkan struktur data yang diterima
            // return response()->json([
            //     'saw' => $sawData['result'] ?? [],
            //     'wp' => $wpData['result'] ?? [],
            //     'topsis' => $topsisData['result'] ?? []
            // ]);
            
            // Pastikan semua data yang diperlukan ada
            if (empty($sawData['result'] ?? []) || empty($wpData['result'] ?? []) || empty($topsisData['result'] ?? [])) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Data hasil perhitungan SAW, WP, atau TOPSIS tidak lengkap. Pastikan Anda telah menghitung semua metode terlebih dahulu.');
            }
            
            // Format data untuk perbandingan
            $perbandingan = $this->formatDataPerbandingan(
                $sawData['result'], // Data dari SAW
                $wpData['result'],   // Data dari WP
                $topsisData['result'] // Data dari TOPSIS
            );
            
            // Log hasil format data
            \Log::info('Hasil format data perbandingan:', ['perbandingan' => $perbandingan]);
            
            // Jika tidak ada data yang valid, redirect ke halaman penilaian
            if (empty($perbandingan)) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Tidak ada data penilaian yang valid untuk dibandingkan. Pastikan data penilaian sudah diisi dengan benar.');
            }
            
            // Hitung statistik deviasi
            $statistik = $this->hitungStatistik($perbandingan);
            
            return view('perbandingan.index', compact('perbandingan', 'statistik'));
            
        } catch (\Exception $e) {
            // Log error untuk keperluan debugging
            \Log::error('Error in PerbandinganController: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Redirect ke halaman sebelumnya dengan pesan error
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses data perbandingan: ' . $e->getMessage());
        }
    }
    
    private function formatDataPerbandingan($saw, $wp, $topsis)
    {
        $data = [];
        
        // Format data SAW
        $sawData = [];
        if (is_array($saw)) {
            foreach ($saw as $item) {
                if (isset($item['skor']) && isset($item['rank'])) {
                    $sawData[$item['kode'] ?? $item['nama']] = [
                        'nilai' => $item['skor'],
                        'rank' => $item['rank'],
                        'nama' => $item['nama'] ?? $item['kode']
                    ];
                }
            }
        }
        
        // Format data WP
        $wpData = [];
        if (is_array($wp)) {
            foreach ($wp as $item) {
                if (isset($item['skor']) && isset($item['rank'])) {
                    $wpData[$item['kode'] ?? $item['nama'] ?? ''] = [
                        'nilai' => $item['skor'],
                        'rank' => $item['rank'],
                        'nama' => $item['nama'] ?? $item['kode']
                    ];
                }
            }
        }
        
        // Format data TOPSIS
        $topsisData = [];
        if (is_array($topsis)) {
            foreach ($topsis as $item) {
                if (isset($item['skor']) && isset($item['rank'])) {
                    $topsisData[$item['kode'] ?? $item['nama'] ?? ''] = [
                        'nilai' => $item['skor'],
                        'rank' => $item['rank'],
                        'nama' => $item['nama'] ?? $item['kode']
                    ];
                }
            }
        }
        
        // Gabungkan semua data
        $allIds = array_unique(array_merge(
            array_keys($sawData),
            array_keys($wpData),
            array_keys($topsisData)
        ));
        
        foreach ($allIds as $id) {
            // Dapatkan nama alternatif dari salah satu data yang tersedia
            $nama = $sawData[$id]['nama'] ?? $wpData[$id]['nama'] ?? $topsisData[$id]['nama'] ?? 'Alternatif ' . $id;
            
            $data[] = [
                'alternatif_id' => $id,
                'alternatif_nama' => $nama,
                'saw_nilai' => $sawData[$id]['nilai'] ?? 0,
                'saw_rank' => $sawData[$id]['rank'] ?? 0,
                'wp_nilai' => $wpData[$id]['nilai'] ?? 0,
                'wp_rank' => $wpData[$id]['rank'] ?? 0,
                'topsis_nilai' => $topsisData[$id]['nilai'] ?? 0,
                'topsis_rank' => $topsisData[$id]['rank'] ?? 0,
            ];
        }
        
        // Urutkan berdasarkan peringkat SAW
        usort($data, function($a, $b) {
            return $a['saw_rank'] <=> $b['saw_rank'];
        });
        
        return $data;
    }
    
    private function hitungStatistik($data)
    {
        $total = count($data);
        $sawWpSame = 0;
        $sawTopsisSame = 0;
        $wpTopsisSame = 0;
        $allSame = 0;
        
        foreach ($data as $item) {
            // Pastikan semua peringkat ada sebelum membandingkan
            if (isset($item['saw_rank']) && isset($item['wp_rank']) && $item['saw_rank'] == $item['wp_rank']) {
                $sawWpSame++;
            }
            
            if (isset($item['saw_rank']) && isset($item['topsis_rank']) && $item['saw_rank'] == $item['topsis_rank']) {
                $sawTopsisSame++;
            }
            
            if (isset($item['wp_rank']) && isset($item['topsis_rank']) && $item['wp_rank'] == $item['topsis_rank']) {
                $wpTopsisSame++;
            }
            
            if (isset($item['saw_rank']) && isset($item['wp_rank']) && isset($item['topsis_rank']) && 
                $item['saw_rank'] == $item['wp_rank'] && $item['saw_rank'] == $item['topsis_rank']) {
                $allSame++;
            }
        }
        
        // Hitung persentase dengan pembulatan 2 angka di belakang koma
        $hitungPersen = function($nilai, $total) {
            return $total > 0 ? round(($nilai / $total) * 100, 2) : 0;
        };
        
        return [
            'total_alternatif' => $total,
            'persen_saw_wp' => $hitungPersen($sawWpSame, $total),
            'persen_saw_topsis' => $hitungPersen($sawTopsisSame, $total),
            'persen_wp_topsis' => $hitungPersen($wpTopsisSame, $total),
            'persen_all_same' => $hitungPersen($allSame, $total),
        ];
    }
}
