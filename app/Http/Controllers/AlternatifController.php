<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlternatifController extends Controller
{
    public function index()
    {
        $alternatifs = Alternatif::orderBy('kode_alternatif')->get();
        $nextCode = 'A' . (Alternatif::count() + 1);
        return view('alternatif.index', compact('alternatifs', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_alternatif' => 'required|unique:alternatifs,kode_alternatif|regex:/^A\d+$/',
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif',
        ], [
            'kode_alternatif.regex' => 'Format kode tidak valid. Harus dalam format A1, A2, dst.',
            'kode_alternatif.unique' => 'Kode alternatif sudah digunakan.',
            'nama_alternatif.unique' => 'Nama alternatif sudah digunakan.'
        ]);

        Alternatif::create($validated);
        return redirect()->route('alternatif.index')
            ->with('success', 'Alternatif berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $alternatif = Alternatif::findOrFail($id);
        
        $validated = $request->validate([
            'kode_alternatif' => 'required|unique:alternatifs,kode_alternatif,' . $id . '|regex:/^A\d+$/',
            'nama_alternatif' => 'required|string|max:255|unique:alternatifs,nama_alternatif,' . $id,
        ], [
            'kode_alternatif.regex' => 'Format kode tidak valid. Harus dalam format A1, A2, dst.',
            'kode_alternatif.unique' => 'Kode alternatif sudah digunakan.',
            'nama_alternatif.unique' => 'Nama alternatif sudah digunakan.'
        ]);

        $alternatif->update($validated);
        return redirect()->route('alternatif.index')
            ->with('success', 'Alternatif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $alternatif = Alternatif::findOrFail($id);
            // Tambahkan pengecekan relasi jika diperlukan
            
            $alternatif->delete();
            return redirect()->route('alternatif.index')
                ->with('success', 'Alternatif berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->route('alternatif.index')
                ->with('error', 'Gagal menghapus alternatif. ' . $e->getMessage());
        }
    }
}
