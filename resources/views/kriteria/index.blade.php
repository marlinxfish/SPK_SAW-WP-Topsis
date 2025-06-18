@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Data Kriteria</h3>
        <button class="btn btn-primary" id="btnTambahKriteria" data-total-bobot="{{ $totalBobot }}">
            <i class="fas fa-plus me-1"></i> Tambah Kriteria
        </button>
        
        @push('scripts')
        <script>
            document.getElementById('btnTambahKriteria').addEventListener('click', function(e) {
                const totalBobot = parseFloat(this.getAttribute('data-total-bobot'));
                if (totalBobot >= 1) {
                    e.preventDefault();
                    alert('Total bobot sudah mencapai 1. Silakan edit bobot kriteria yang ada terlebih dahulu.');
                    return false;
                } else {
                    // If total weight is less than 1, show the modal
                    const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
                    modal.show();
                }
            });
        </script>
        @endpush
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="15%">Kode</th>
                            <th>Nama Kriteria</th>
                            <th class="text-center" width="15%">Bobot</th>
                            <th class="text-center" width="15%">Sifat</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kriterias as $k)
                            <tr>
                                <td class="text-center fw-bold">{{ $k->kode_kriteria }}</td>
                                <td>{{ $k->nama_kriteria }}</td>
                                <td class="text-center">{{ number_format($k->bobot, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $k->sifat == 'benefit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($k->sifat) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $k->id }}">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('kriteria.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada data kriteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Total Bobot -->
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>Total Bobot:</strong>
                    <span class="ms-2 badge bg-{{ $totalBobot == 1 ? 'success' : ($totalBobot > 1 ? 'danger' : 'primary') }}">
                        {{ number_format($totalBobot, 2) }} / 1.00
                    </span>
                </div>
                <div class="col-md-6 text-end">
                    @if($totalBobot >= 1)
                        <span class="text-danger">
                            <i class="fas fa-info-circle"></i> Total bobot sudah mencapai 1. Silakan edit bobot kriteria yang ada.
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('kriteria.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kriteria</label>
                        <input type="text" name="kode_kriteria" class="form-control" value="{{ $nextCode }}" readonly>
                        <div class="form-text">Kode akan di-generate otomatis (format: C1, C2, dst)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kriteria" class="form-control" required 
                               placeholder="Masukkan nama kriteria" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot <span class="text-danger">*</span></label>
                        <input type="number" name="bobot" class="form-control" step="0.01" min="0.01" max="1" 
                               placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sifat <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            <option value="benefit">Benefit</option>
                            <option value="cost">Cost</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @foreach($kriterias as $k)
    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit{{ $k->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('kriteria.update', $k->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" value="{{ $k->kode_kriteria }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kriteria" class="form-control" 
                               value="{{ $k->nama_kriteria }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot <span class="text-danger">*</span></label>
                        <input type="number" name="bobot" class="form-control" step="0.01" 
                               min="0.01" max="1" value="{{ number_format($k->bobot, 2) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sifat <span class="text-danger">*</span></label>
                        <select name="sifat" class="form-select" required>
                            <option value="benefit" {{ $k->sifat == 'benefit' ? 'selected' : '' }}>Benefit</option>
                            <option value="cost" {{ $k->sifat == 'cost' ? 'selected' : '' }}>Cost</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    @push('styles')
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.4em 0.75em;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Auto-focus first input when modal is shown
        document.getElementById('modalTambah').addEventListener('shown.bs.modal', function () {
            document.querySelector('#modalTambah [name="nama_kriteria"]').focus();
        });
    </script>
    @endpush
@endsection
