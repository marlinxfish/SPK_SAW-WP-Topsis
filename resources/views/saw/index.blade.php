@extends('layouts.app')

@section('title', 'Perhitungan SAW')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perhitungan SAW</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Hasil Perhitungan Metode SAW</h5>
        <a href="{{ route('penilaian.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Hasil perhitungan menggunakan metode Simple Additive Weighting (SAW)
        </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Matriks Keputusan -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Matriks Keputusan (X)</h5>
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
                                    <td class="text-center">{{ $matrix[$alt->id][$krit->id] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Normalisasi -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Matriks Ternormalisasi (R)</h5>
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
                                    <td class="text-center">{{ number_format($normal[$alt->id][$krit->id] ?? 0, 4) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Hasil Akhir -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Hasil Perangkingan</h5>
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
                            <th class="text-center">Skor Akhir (Vi)</th>
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
            <h5 class="mb-0">Keterangan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Kriteria:</h6>
                    <ul class="list-unstyled">
                        @foreach($kriterias as $kriteria)
                            <li>
                                <strong>{{ $kriteria->kode_kriteria }}</strong>: {{ $kriteria->nama_kriteria }}
                                <span class="badge bg-{{ $kriteria->sifat == 'benefit' ? 'success' : 'danger' }}">
                                    {{ ucfirst($kriteria->sifat) }} ({{ $kriteria->bobot * 100 }}%)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Keterangan:</h6>
                    <ul>
                        <li>Metode SAW (Simple Additive Weighting) digunakan untuk menentukan peringkat alternatif berdasarkan kriteria yang telah ditentukan.</li>
                        <li>Skor akhir (Vi) dihitung dengan rumus: Σ(Rij × Wj) dimana Rij adalah nilai ternormalisasi dan Wj adalah bobot kriteria.</li>
                        <li>Alternatif dengan skor tertinggi merupakan rekomendasi terbaik.</li>
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
    .nav-pills .nav-link {
        color: #495057;
    }
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }
    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }
    .sidebar .nav-link[data-bs-toggle="collapse"]::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        float: right;
        transition: transform 0.3s;
    }
    .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
        transform: rotate(180deg);
    }
</style>
@endpush