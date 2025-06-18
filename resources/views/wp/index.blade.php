@extends('layouts.app')

@section('title', 'Perhitungan WP')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Perhitungan WP</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Hasil Perhitungan Metode Weighted Product (WP)</h5>
        <a href="{{ route('penilaian.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Hasil perhitungan menggunakan metode Weighted Product (WP)
        </div>

        <!-- Tabel Matriks Keputusan -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
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

        <!-- Tabel Bobot Ternormalisasi -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Bobot Ternormalisasi (Wj')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                @foreach($kriterias as $kriteria)
                                    <th class="text-center">
                                        {{ $kriteria->kode_kriteria }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($kriterias as $krit)
                                    <td class="text-center">{{ number_format($bobotTernormalisasi[$krit->id], 4) }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Perhitungan Vektor S -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Detail Perhitungan Vektor S (S = ∏(Xij^Wj'))</h5>
            </div>
            <div class="card-body p-0">
                @foreach($alternatifs as $alt)
                    <div class="mb-4">
                        <h6 class="px-3 pt-3 mb-0 fw-bold">{{ $alt->kode_alternatif }} - {{ $alt->nama_alternatif }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th class="text-center">Nilai (Xij)</th>
                                        <th class="text-center">Bobot (Wj')</th>
                                        <th class="text-center">Perhitungan (Xij^Wj')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $perkalian = 1;
                                    @endphp
                                    @foreach($kriterias as $krit)
                                        @php
                                            $xij = $matrix[$alt->id][$krit->id] ?? 0;
                                            $xij = $xij == 0 ? 0.01 : $xij; // Hindari error log(0)
                                            $wj = $bobotTernormalisasi[$krit->id];
                                            
                                            if ($krit->sifat == 'benefit') {
                                                $hitung = pow($xij, $wj);
                                            } else {
                                                $hitung = pow($xij, -$wj);
                                            }
                                            $perkalian *= $hitung;
                                        @endphp
                                        <tr>
                                            <td>{{ $krit->kode_kriteria }} ({{ $krit->sifat }})</td>
                                            <td class="text-center">{{ $xij == 0.01 ? '0' : $xij }}</td>
                                            <td class="text-center">{{ number_format($wj, 4) }}</td>
                                            <td class="text-center">
                                                @if($krit->sifat == 'benefit')
                                                    {{ $xij }}<sup>{{ number_format($wj, 4) }}</sup>
                                                @else
                                                    {{ $xij }}<sup>-{{ number_format($wj, 4) }}</sup>
                                                @endif
                                                = {{ number_format($hitung, 6) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="3" class="text-end">Hasil Perkalian (S):</td>
                                        <td class="text-center">{{ number_format($perkalian, 6) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-0">
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Tabel Vektor S -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Vektor S dan V (Hasil Akhir)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <th class="text-center">Nilai S</th>
                                <th class="text-center">Nilai V (Vi)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alternatifs as $alt)
                                @php
                                    $vektorV = $vektorS[$alt->id] / $totalVektorS;
                                    $vektorV = number_format($vektorV, 6);
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $alt->kode_alternatif }} - {{ $alt->nama_alternatif }}</td>
                                    <td class="text-center">{{ number_format($vektorS[$alt->id], 6) }}</td>
                                    <td class="text-center fw-bold">{{ $vektorV }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-secondary fw-bold">
                                <td>Total</td>
                                <td class="text-center">{{ number_format($totalVektorS, 6) }}</td>
                                <td class="text-center">1.000000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hasil Akhir -->
        <div class="card">
            <div class="card-header bg-primary text-white">
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
                                <th class="text-center">Skor Akhir (V)</th>
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
                <h5 class="mb-0">Keterangan Perhitungan Weighted Product</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Langkah-langkah Perhitungan:</h6>
                        <ol>
                            <li>Menentukan nilai vektor S dengan rumus:<br>
                                <code>S = ∏(Xij^Wj') untuk benefit, atau S = ∏(Xij^-Wj') untuk cost</code>
                            </li>
                            <li>Menjumlahkan semua vektor S</li>
                            <li>Menghitung vektor V dengan rumus:<br>
                                <code>V = S / ΣS</code>
                            </li>
                            <li>Mengurutkan hasil dari nilai V terbesar ke terkecil</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6>Keterangan Simbol:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Xij</strong>: Nilai alternatif i pada kriteria j</li>
                            <li><strong>Wj'</strong>: Bobot ternormalisasi kriteria j</li>
                            <li><strong>S</strong>: Vektor S (perkalian kriteria)</li>
                            <li><strong>V</strong>: Vektor V (hasil akhir)</li>
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
