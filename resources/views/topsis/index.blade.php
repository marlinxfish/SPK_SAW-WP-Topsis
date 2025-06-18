@extends('layouts.app')

@section('title', 'Perhitungan TOPSIS')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perhitungan TOPSIS</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Hasil Perhitungan Metode TOPSIS</h5>
        <a href="{{ route('penilaian.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Hasil perhitungan menggunakan metode TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)
        </div>

        <!-- Tabel Matriks Keputusan -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">1. Matriks Keputusan (X)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                @foreach($kriterias as $kriteria)
                                    <th class="text-center">
                                        {{ $kriteria->kode_kriteria }}
                                        <br>
                                        <small class="text-muted">({{ $kriteria->sifat }})</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alternatifs as $alt)
                                <tr>
                                    <td class="fw-bold">{{ $alt->kode_alternatif }} - {{ $alt->nama_alternatif }}</td>
                                    @foreach($kriterias as $krit)
                                        <td class="text-center">{{ number_format($matriksKeputusan[$alt->id][$krit->id], 4) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Matriks Ternormalisasi -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">2. Matriks Ternormalisasi (R)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                @foreach($kriterias as $kriteria)
                                    <th class="text-center">
                                        {{ $kriteria->kode_kriteria }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alternatifs as $alt)
                                <tr>
                                    <td class="fw-bold">{{ $alt->kode_alternatif }}</td>
                                    @foreach($kriterias as $krit)
                                        <td class="text-center">{{ number_format($matriksNormalisasi[$alt->id][$krit->id], 6) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Matriks Ternormalisasi Terbobot -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">3. Matriks Ternormalisasi Terbobot (Y)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                @foreach($kriterias as $kriteria)
                                    <th class="text-center">
                                        {{ $kriteria->kode_kriteria }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alternatifs as $alt)
                                <tr>
                                    <td class="fw-bold">{{ $alt->kode_alternatif }}</td>
                                    @foreach($kriterias as $krit)
                                        <td class="text-center">{{ number_format($matriksTerbobot[$alt->id][$krit->id], 6) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Solusi Ideal -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">4. Solusi Ideal Positif (A+)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @foreach($kriterias as $kriteria)
                                            <th class="text-center">{{ $kriteria->kode_kriteria }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($kriterias as $krit)
                                            <td class="text-center">{{ number_format($solusiIdealPositif[$krit->id], 6) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">5. Solusi Ideal Negatif (A-)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @foreach($kriterias as $kriteria)
                                            <th class="text-center">{{ $kriteria->kode_kriteria }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($kriterias as $krit)
                                            <td class="text-center">{{ number_format($solusiIdealNegatif[$krit->id], 6) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jarak ke Solusi Ideal -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">6. Jarak ke Solusi Ideal Positif (D+)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alternatif</th>
                                        <th class="text-center">Nilai D+</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alternatifs as $alt)
                                        <tr>
                                            <td class="fw-bold">{{ $alt->kode_alternatif }} - {{ $alt->nama_alternatif }}</td>
                                            <td class="text-center">{{ number_format($jarakPositif[$alt->id], 6) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">7. Jarak ke Solusi Ideal Negatif (D-)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alternatif</th>
                                        <th class="text-center">Nilai D-</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alternatifs as $alt)
                                        <tr>
                                            <td class="fw-bold">{{ $alt->kode_alternatif }} - {{ $alt->nama_alternatif }}</td>
                                            <td class="text-center">{{ number_format($jarakNegatif[$alt->id], 6) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Akhir -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">8. Hasil Perangkingan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Peringkat</th>
                                <th>Kode</th>
                                <th>Nama Alternatif</th>
                                <th class="text-center">Nilai Preferensi (V)</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result as $index => $item)
                                <tr class="{{ $loop->first ? 'table-success fw-bold' : '' }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        @if($item['rank'] == 1)
                                            <span class="badge bg-warning text-dark"><i class="fas fa-trophy me-1"></i> {{ $item['rank'] }}</span>
                                        @elseif($item['rank'] == 2)
                                            <span class="badge bg-secondary"><i class="fas fa-medal me-1"></i> {{ $item['rank'] }}</span>
                                        @elseif($item['rank'] == 3)
                                            <span class="badge bg-danger"><i class="fas fa-award me-1"></i> {{ $item['rank'] }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $item['rank'] }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item['kode'] }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($item['skor'], 4) }}</span>
                                    </td>
                                    <td>
                                        @if($item['rank'] == 1)
                                            <span class="badge bg-success"><i class="fas fa-star me-1"></i> Rekomendasi Terbaik</span>
                                        @elseif($item['rank'] <= 3)
                                            <span class="badge bg-info"><i class="fas fa-thumbs-up me-1"></i> Direkomendasikan</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Keterangan Perhitungan TOPSIS</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Langkah-langkah Perhitungan:</h6>
                        <ol>
                            <li>Membuat matriks keputusan (X)</li>
                            <li>Menormalisasi matriks (R)</li>
                            <li>Menghitung matriks ternormalisasi terbobot (Y)</li>
                            <li>Menentukan solusi ideal positif (A+) dan negatif (A-)</li>
                            <li>Menghitung jarak ke solusi ideal positif (D+) dan negatif (D-)</li>
                            <li>Menghitung nilai preferensi (V)</li>
                            <li>Mengurutkan berdasarkan nilai preferensi tertinggi</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6>Keterangan Simbol:</h6>
                        <ul class="list-unstyled">
                            <li><strong>X</strong>: Matriks keputusan</li>
                            <li><strong>R</strong>: Matriks ternormalisasi</li>
                            <li><strong>Y</strong>: Matriks ternormalisasi terbobot</li>
                            <li><strong>A+</strong>: Solusi ideal positif</li>
                            <li><strong>A-</strong>: Solusi ideal negatif</li>
                            <li><strong>D+</strong>: Jarak ke solusi ideal positif</li>
                            <li><strong>D-</strong>: Jarak ke solusi ideal negatif</li>
                            <li><strong>V</strong>: Nilai preferensi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .card {
        margin-bottom: 1.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        font-weight: 600;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush
