@extends('layouts.app')

@section('title', 'Perbandingan Metode SPK')

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(empty($perbandingan) || empty($statistik))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Data perbandingan tidak tersedia atau belum dihitung. Pastikan Anda telah mengisi data penilaian dan menghitung hasil SAW, WP, dan TOPSIS terlebih dahulu.
    </div>
@else
{{--
@php
    \Log::info('Data perbandingan:', ['perbandingan' => $perbandingan, 'statistik' => $statistik]);
    // Uncomment baris di bawah untuk menampilkan data debug langsung di halaman
    // echo '<pre>' . print_r(compact('perbandingan', 'statistik'), true) . '</pre>';
@endphp
--}}
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistik Perbandingan Metode</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Kesamaan SAW & WP</h6>
                                    <h3 class="text-primary">{{ $statistik['persen_saw_wp'] }}%</h3>
                                    <small class="text-muted">dari {{ $statistik['total_alternatif'] }} data</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Kesamaan SAW & TOPSIS</h6>
                                    <h3 class="text-success">{{ $statistik['persen_saw_topsis'] }}%</h3>
                                    <small class="text-muted">dari {{ $statistik['total_alternatif'] }} data</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Kesamaan WP & TOPSIS</h6>
                                    <h3 class="text-info">{{ $statistik['persen_wp_topsis'] }}%</h3>
                                    <small class="text-muted">dari {{ $statistik['total_alternatif'] }} data</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Kesamaan Ketiga Metode</h6>
                                    <h3 class="text-warning">{{ $statistik['persen_all_same'] }}%</h3>
                                    <small class="text-muted">dari {{ $statistik['total_alternatif'] }} data</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tabel Perbandingan Hasil</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tabelPerbandingan">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">Alternatif</th>
                                    <th colspan="2" class="text-center">Metode SAW</th>
                                    <th colspan="2" class="text-center">Metode WP</th>
                                    <th colspan="2" class="text-center">Metode TOPSIS</th>
                                    <th rowspan="2" class="align-middle">Deviasi</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Peringkat</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Peringkat</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Peringkat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perbandingan as $item)
                                @php
                                    // Pastikan semua peringkat ada sebelum menghitung deviasi
                                    $deviasi = 0;
                                    if (isset($item['saw_rank']) && isset($item['wp_rank']) && isset($item['topsis_rank'])) {
                                        $deviasi = abs($item['saw_rank'] - $item['wp_rank']) + 
                                                  abs($item['saw_rank'] - $item['topsis_rank']) + 
                                                  abs($item['wp_rank'] - $item['topsis_rank']);
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $item['alternatif_nama'] }}</strong></td>
                                    <td class="text-end">{{ number_format($item['saw_nilai'], 4) }}</td>
                                    <td class="text-center">{{ $item['saw_rank'] }}</td>
                                    <td class="text-end">{{ number_format($item['wp_nilai'], 4) }}</td>
                                    <td class="text-center">{{ $item['wp_rank'] }}</td>
                                    <td class="text-end">{{ number_format($item['topsis_nilai'], 4) }}</td>
                                    <td class="text-center">{{ $item['topsis_rank'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $deviasi == 0 ? 'success' : ($deviasi <= 2 ? 'warning' : 'danger') }}">
                                            {{ $deviasi }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data untuk ditampilkan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .table th {
        font-weight: 600;
        background-color: #f8f9fc;
    }
    .badge {
        min-width: 30px;
        padding: 0.35em 0.5em;
        font-size: 0.85em;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#tabelPerbandingan').DataTable({
            responsive: true,
            order: [[1, 'desc']], // Default urutkan berdasarkan nilai SAW
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [0, 7] } // Non-aktifkan pengurutan untuk kolom Alternatif dan Deviasi
            ]
        });
    });
</script>
@endpush
@endsection
