<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriterias->sum('bobot');
        $nextCode = 'C' . (Kriteria::count() + 1);
        
        return view('kriteria.index', compact('kriterias', 'nextCode', 'totalBobot'));
    }

    public function store(Request $request)
    {
        $totalBobot = Kriteria::sum('bobot');
        
        $validated = $request->validate([
            'kode_kriteria' => 'required|unique:kriterias,kode_kriteria|regex:/^C\d+$/',
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => [
                'required',
                'numeric',
                'min:0.01',
                'max:1',
                function ($attribute, $value, $fail) use ($totalBobot) {
                    $newTotal = $totalBobot + $value;
                    if (bccomp($newTotal, 1, 2) > 0) {
                        $sisaBobot = 1 - $totalBobot;
                        $fail("Total bobot akan menjadi " . number_format($newTotal, 2) . 
                              ". Melebihi batas maksimum 1. Sisa bobot yang tersedia: " . 
                              number_format($sisaBobot, 2));
                    }
                },
            ],
            'sifat' => 'required|in:benefit,cost',
        ], [
            'kode_kriteria.regex' => 'Format kode tidak valid. Harus dalam format C1, C2, dst.',
            'bobot.min' => 'Bobot minimal 0.01',
            'bobot.max' => 'Bobot maksimal 1',
            'bobot.numeric' => 'Bobot harus berupa angka',
        ]);

        Kriteria::create($validated);
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $totalBobot = Kriteria::where('id', '!=', $id)->sum('bobot');
        
        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => [
                'required',
                'numeric',
                'min:0.01',
                'max:1',
                function ($attribute, $value, $fail) use ($totalBobot) {
                    $newTotal = $totalBobot + $value;
                    // Gunakan bccomp untuk membandingkan angka desimal dengan presisi 2 digit
                    if (bccomp($newTotal, 1, 2) > 0) {
                        $sisaBobot = 1 - $totalBobot;
                        $fail("Total bobot akan menjadi " . number_format($newTotal, 2) . 
                              ". Melebihi batas maksimum 1. Sisa bobot yang tersedia: " . 
                              number_format($sisaBobot, 2));
                    }
                },
            ],
            'sifat' => 'required|in:benefit,cost',
        ], [
            'bobot.min' => 'Bobot minimal 0.01',
            'bobot.max' => 'Bobot maksimal 1',
        ]);

        $kriteria->update($validated);
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        try {
            $kriteria = Kriteria::findOrFail($id);
            
            $kriteria->delete();
            
            return redirect()->route('kriteria.index')
                ->with('success', 'Kriteria berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->route('kriteria.index')
                ->with('error', 'Gagal menghapus kriteria. ' . $e->getMessage());
        }
    }
}
