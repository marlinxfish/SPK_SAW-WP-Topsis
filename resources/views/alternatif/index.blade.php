@extends('layouts.app')

@section('title', 'Data Alternatif')

@section('header-actions')
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus me-1"></i> Tambah Alternatif
    </button>
@endsection

@section('content')

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">Kode</th>
                            <th>Nama Alternatif</th>
                            <th class="text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alternatifs as $a)
                            <tr>
                                <td class="text-center fw-bold">{{ $a->kode_alternatif }}</td>
                                <td>{{ $a->nama_alternatif }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-id="{{ $a->id }}"
                                                data-kode="{{ $a->kode_alternatif }}"
                                                data-nama="{{ $a->nama_alternatif }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('alternatif.destroy', $a->id) }}" method="POST" onsubmit="event.preventDefault(); deleteItem(this, 'Apakah Anda yakin ingin menghapus alternatif ini?')">
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
                                <td colspan="3" class="text-center py-4">Tidak ada data alternatif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Inisialisasi modal edit
        document.addEventListener('DOMContentLoaded', function() {
            const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));
            
            // Menangani klik tombol edit
            document.querySelectorAll('[data-bs-target="#modalEdit"]').forEach(button => {
                button.addEventListener('click', function() {
                    const alternatifId = this.getAttribute('data-id');
                    const kode = this.getAttribute('data-kode');
                    const nama = this.getAttribute('data-nama');
                    const form = document.getElementById('formEdit');
                    
                    // Update form action dengan ID yang benar
                    form.action = `/alternatif/${alternatifId}`;
                    
                    // Isi form dengan data alternatif
                    document.getElementById('kode_alternatif_edit').value = kode;
                    document.getElementById('nama_alternatif_edit').value = nama;
                    
                    // Tampilkan modal
                    modalEdit.show();
                });
            });
        });
    </script>
    @endpush
    
    @push('styles')
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
    </style>
    @endpush

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('alternatif.store') }}" method="POST" id="formTambah">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Alternatif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_alternatif" class="form-label">Kode Alternatif</label>
                            <input type="text" class="form-control" id="kode_alternatif" 
                                   name="kode_alternatif" value="{{ $nextCode }}" readonly required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_alternatif" class="form-label">Nama Alternatif</label>
                            <input type="text" class="form-control" id="nama_alternatif" 
                                   name="nama_alternatif" required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tunggal -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Alternatif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_alternatif_edit" class="form-label">Kode Alternatif</label>
                            <input type="text" class="form-control" id="kode_alternatif_edit" 
                                   name="kode_alternatif" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_alternatif_edit" class="form-label">Nama Alternatif</label>
                            <input type="text" class="form-control" id="nama_alternatif_edit" 
                                   name="nama_alternatif" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
