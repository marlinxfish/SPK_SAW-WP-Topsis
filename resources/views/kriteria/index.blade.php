@extends('layouts.app')

@section('title', 'Data Kriteria')

@section('header-actions')
    <button class="btn btn-primary btn-sm" id="btnTambahKriteria" data-total-bobot="{{ $totalBobot }}">
        <i class="fas fa-plus me-1"></i> Tambah Kriteria
    </button>
    
    @push('scripts')
    <script>
        document.getElementById('btnTambahKriteria').addEventListener('click', function(e) {
            const totalBobot = parseFloat(this.getAttribute('data-total-bobot'));
            if (totalBobot >= 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Total bobot sudah mencapai 1. Silakan edit bobot kriteria yang ada terlebih dahulu.',
                    confirmButtonColor: '#4e73df',
                });
                return false;
            } else {
                const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
                modal.show();
            }
        });
    </script>
    @endpush
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $k->id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('kriteria.destroy', $k->id) }}" method="POST" onsubmit="event.preventDefault(); deleteItem(this, 'Apakah Anda yakin ingin menghapus kriteria ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
    @push('scripts')
    <script>
        function deleteItem(form, message) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    @endpush
    
    <style>
        .table th, .table td {
            vertical-align: middle;
            padding: 1rem;
        }
        
        .btn-sm {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .table thead th {
            background-color: #f8f9fc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
        }
        .badge {
            font-size: 0.75em;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }
        .btn-sm {
            padding: 0.3rem 0.65rem;
            font-size: 0.8rem;
            border-radius: 0.35rem;
        }
        .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.2s;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(58, 59, 69, 0.15);
        }
        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.15);
        }
        .modal-header {
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
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
