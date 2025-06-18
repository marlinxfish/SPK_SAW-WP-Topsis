<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiMatrix;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index()
    {
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();
        $kriterias = Kriteria::orderBy('kode_kriteria')->get();

        return view('penilaian.index', compact('alternatifs', 'kriterias'));
    }

    public function store(Request $request)
    {
        // Bersihkan semua flash message
        $request->session()->forget(['success', 'errors', 'error']);
        
        // Ambil data dari request
        $updates = $request->input('updates', []);
        $savedCount = 0;
        $errors = [];
        
        if (!is_array($updates) || empty($updates)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang akan disimpan'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($updates as $index => $update) {
                try {
                    // Validasi data
                    $validator = \Validator::make($update, [
                        'alternatif_id' => 'required|integer|exists:alternatifs,id',
                        'kriteria_id' => 'required|integer|exists:kriterias,id',
                        'nilai' => 'nullable|numeric|min:0'
                    ]);
                    
                    if ($validator->fails()) {
                        $errors[] = [
                            'index' => $index,
                            'errors' => $validator->errors()->toArray()
                        ];
                        continue;
                    }
                    
                    // Proses penyimpanan
                    if ($update['nilai'] === null || $update['nilai'] === '') {
                        // Hapus jika nilai kosong
                        $deleted = NilaiMatrix::where('alternatif_id', $update['alternatif_id'])
                                  ->where('kriteria_id', $update['kriteria_id'])
                                  ->delete();
                        if ($deleted) $savedCount++;
                    } else {
                        // Update atau buat baru
                        $result = NilaiMatrix::updateOrCreate(
                            [
                                'alternatif_id' => $update['alternatif_id'],
                                'kriteria_id' => $update['kriteria_id']
                            ],
                            ['nilai' => (float)$update['nilai']]
                        );
                        if ($result) $savedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => $e->getMessage()
                    ];
                    continue;
                }
            }
            
            DB::commit();
            
            $response = [
                'success' => true,
                'message' => 'Berhasil menyimpan ' . $savedCount . ' data',
                'saved_count' => $savedCount
            ];
            
            if (!empty($errors)) {
                $response['partial_success'] = true;
                $response['error_count'] = count($errors);
                $response['message'] .= ' dengan ' . count($errors) . ' error';
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving penilaian: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'error_details' => $errors
            ], 500);
        }
    }

    public function getNilai($alternatifId, $kriteriaId)
    {
        $nilai = NilaiMatrix::where('alternatif_id', $alternatifId)
                          ->where('kriteria_id', $kriteriaId)
                          ->first();

        return response()->json([
            'nilai' => $nilai ? $nilai->nilai : null
        ]);
    }
    
    /**
     * Memeriksa kelengkapan data penilaian
     */
    public function checkCompleteness()
    {
        $totalAlternatif = Alternatif::count();
        $totalKriteria = Kriteria::count();
        $totalNilai = NilaiMatrix::count();
        
        // Hitung jumlah nilai yang seharusnya ada (setiap alternatif harus memiliki nilai untuk setiap kriteria)
        $expectedTotalNilai = $totalAlternatif * $totalKriteria;
        
        // Periksa apakah semua data sudah lengkap
        $isComplete = ($totalAlternatif > 0) && ($totalKriteria > 0) && ($totalNilai >= $expectedTotalNilai);
        
        return response()->json([
            'complete' => $isComplete,
            'total_alternatif' => $totalAlternatif,
            'total_kriteria' => $totalKriteria,
            'total_nilai' => $totalNilai,
            'expected_total_nilai' => $expectedTotalNilai
        ]);
    }
}
