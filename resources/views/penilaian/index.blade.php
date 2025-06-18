@extends('layouts.app')

@section('title', 'Penilaian Alternatif')

@section('header-actions')
    <div>
        <button type="button" id="btnReset" class="btn btn-outline-secondary btn-sm me-2">
            <i class="fas fa-undo me-1"></i> Reset
        </button>
        <button type="button" id="btnSimpan" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Simpan Perubahan
        </button>
    </div>
@endsection

@section('content')
    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                <table class="table table-hover align-middle mb-0" style="min-width: 1200px;">
                    <thead>
                        <tr>
                            <th style="width: 250px; min-width: 250px;">Alternatif</th>
                            @foreach($kriterias as $kriteria)
                                <th class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold">{{ $kriteria->kode_kriteria }}</span>
                                        <small class="text-muted text-center">{{ $kriteria->nama_kriteria }}</small>
                                        <span class="badge bg-{{ $kriteria->sifat == 'benefit' ? 'success' : 'danger' }} mt-1">
                                        {{ ucfirst($kriteria->sifat) }}
                                    </small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alternatifs as $alternatif)
                            <tr>
                                <td class="fw-bold">
                                    {{ $alternatif->kode_alternatif }} - {{ $alternatif->nama_alternatif }}
                                </td>
                                @foreach($kriterias as $kriteria)
                                    <td class="text-center">
                                        <input type="number" 
                                               class="form-control form-control-sm text-center nilai-input" 
                                               data-alternatif-id="{{ $alternatif->id }}"
                                               data-kriteria-id="{{ $kriteria->id }}"
                                               value="{{ \App\Models\NilaiMatrix::where('alternatif_id', $alternatif->id)->where('kriteria_id', $kriteria->id)->first()?->nilai ?? '' }}"
                                               min="0"
                                               step="1">
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($kriterias) + 1 }}" class="text-center py-4">Tidak ada data alternatif</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Style untuk alert container */
    #alertContainer {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1100;
        max-width: 400px;
    }
    
    /* Style untuk tabel */
    .table th, .table td {
        vertical-align: middle;
        padding: 1rem;
        white-space: nowrap;
    }
    
    .table td:first-child {
        position: sticky;
        left: 0;
        background-color: #fff;
        z-index: 1;
    }
    
    .table th:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
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
        font-size: 0.7em;
        font-weight: 600;
        padding: 0.3em 0.6em;
        border-radius: 0.25rem;
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
    
    /* Style untuk input number */
    input[type="number"] {
        text-align: center;
        min-width: 80px;
        -moz-appearance: textfield;
    }
    
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    /* Style untuk tombol aksi */
    .btn-sm {
        padding: 0.3rem 0.65rem;
        font-size: 0.8rem;
        border-radius: 0.35rem;
    }
    
    /* Style untuk alert container */
    #alertContainer {
        position: fixed;
        top: 70px; /* Sesuaikan dengan tinggi navbar */
        right: 20px;
        z-index: 1050; /* Pastikan di atas elemen lain */
        width: 350px;
        max-width: 100%;
    }
    
    /* Animasi untuk alert */
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .alert {
        animation: slideInRight 0.3s ease-out;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
    }
    
    /* Style untuk input yang berubah */
    .nilai-input.is-changed,
    .nilai-input.is-changed:focus,
    .nilai-input.is-changed:hover,
    .nilai-input.is-changed:active,
    .nilai-input.is-changed:focus-visible {
        background-color: #fff3cd !important;
        background-image: none !important;
        border: 2px solid #ffc107 !important;
        font-weight: 500 !important;
        color: #000 !important;
        -webkit-box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.5) !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.5) !important;
        outline: none !important;
    }
    
    .nilai-input.is-changed:focus,
    .nilai-input.is-changed:focus-visible {
        border-color: #ff9800 !important;
        -webkit-box-shadow: 0 0 0 0.25rem rgba(255, 152, 0, 0.5) !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 152, 0, 0.5) !important;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Inisialisasi variabel
    let changedInputs = new Set();
    let isSaving = false;
    
    // Fungsi untuk update tampilan tombol simpan
    function updateSaveButton() {
        const $btn = $('#btnSimpan');
        if (changedInputs.size > 0 && !isSaving) {
            $btn.prop('disabled', false);
        } else {
            $btn.prop('disabled', true);
        }
    }
    
    // Fungsi untuk menyimpan perubahan ke session storage
    function saveToSession(input) {
        const alternatifId = input.data('alternatif-id');
        const kriteriaId = input.data('kriteria-id');
        const inputId = `${alternatifId}-${kriteriaId}`;
        const value = input.val().trim();
        const originalValue = input.attr('data-original-value') || '';
        
        // Simpan ke session storage
        if (value !== originalValue) {
            sessionStorage.setItem(`penilaian_${inputId}`, value);
        } else {
            sessionStorage.removeItem(`penilaian_${inputId}`);
        }
        
        // Tandai sebagai berubah
        if (value !== originalValue) {
            changedInputs.add(inputId);
            input.addClass('is-changed');
            input.css({
                'background-color': '#fff3cd',
                'border': '2px solid #ffc107',
                'font-weight': '500',
                'color': '#000'
            });
        } else {
            changedInputs.delete(inputId);
            input.removeClass('is-changed');
            input.css({
                'background-color': '',
                'border': '',
                'font-weight': '',
                'color': ''
            });
        }
        
        updateSaveButton();
    }
    
    // Fungsi untuk mereset nilai ke nilai asli
    function resetToOriginalValues() {
        $('.nilai-input').each(function() {
            const input = $(this);
            const originalValue = input.attr('data-original-value') || '';
            input.val(originalValue);
            
            // Hapus dari daftar perubahan
            const inputId = `${input.data('alternatif-id')}-${input.data('kriteria-id')}`;
            changedInputs.delete(inputId);
            
            // Reset tampilan
            input.removeClass('is-changed');
            input.css({
                'background-color': '',
                'border': '',
                'font-weight': '',
                'color': ''
            });
            
            // Hapus dari session storage
            sessionStorage.removeItem(`penilaian_${inputId}`);
        });
        
        updateSaveButton();
        return true;
    }
    
    // Fungsi untuk mengirim data ke server
    function saveChanges() {
        if (isSaving || changedInputs.size === 0) return;
        
        const $btn = $('#btnSimpan');
        const updates = [];
        
        // Kumpulkan semua perubahan dari input yang diubah
        $('.nilai-input.is-changed').each(function() {
            const input = $(this);
            const alternatifId = input.data('alternatif-id');
            const kriteriaId = input.data('kriteria-id');
            const nilai = input.val().trim() === '' ? null : parseFloat(input.val().trim());
            
            updates.push({
                alternatif_id: alternatifId,
                kriteria_id: kriteriaId,
                nilai: nilai
            });
        });
        
        // Tandai sedang menyimpan
        isSaving = true;
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');
        
        // Kirim ke server
        $.ajax({
            url: '{{ route("penilaian.store") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                updates: updates
            },
            success: function(response) {
                if (response.success) {
                    // Update nilai asli untuk input yang berhasil disimpan
                    updates.forEach(update => {
                        const input = $(`.nilai-input[data-alternatif-id="${update.alternatif_id}"][data-kriteria-id="${update.kriteria_id}"]`);
                        input.attr('data-original-value', update.nilai !== null ? update.nilai : '');
                        
                        // Hapus dari daftar perubahan
                        const inputId = `${update.alternatif_id}-${update.kriteria_id}`;
                        changedInputs.delete(inputId);
                        
                        // Reset tampilan
                        input.removeClass('is-changed');
                        input.css({
                            'background-color': '',
                            'border': '',
                            'font-weight': '',
                            'color': ''
                        });
                        
                        // Hapus dari session storage
                        sessionStorage.removeItem(`penilaian_${inputId}`);
                    });
                    
                    showAlert('Data berhasil disimpan', 'success');
                } else {
                    showAlert('Gagal menyimpan: ' + (response.message || 'Terjadi kesalahan'), 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            },
            complete: function() {
                isSaving = false;
                $btn.html('<i class="fas fa-save me-1"></i> Simpan Perubahan');
                updateSaveButton();
            }
        });
    }
    
    // Fungsi untuk menampilkan notifikasi
    function showAlert(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Inisialisasi data original value dan event listener
    $('.nilai-input').each(function() {
        const input = $(this);
        // Simpan nilai asli dari database
        const originalValue = input.val() === '' ? '' : input.val();
        input.attr('data-original-value', originalValue);
        
        // Set nilai dari session storage jika ada
        const inputId = `${input.data('alternatif-id')}-${input.data('kriteria-id')}`;
        const savedValue = sessionStorage.getItem(`penilaian_${inputId}`);
        
        if (savedValue !== null) {
            input.val(savedValue);
        }
        
        // Tambahkan event listener untuk input
        input.on('input', function() {
            saveToSession($(this));
        });
    });
    
    // Update tombol simpan setelah inisialisasi
    updateSaveButton();
    
    // Handle tombol reset
    $('#btnReset').on('click', function() {
        Swal.fire({
            title: 'Konfirmasi Reset',
            text: 'Apakah Anda yakin ingin mereset semua perubahan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                resetToOriginalValues();
                showAlert('Semua perubahan telah direset ke nilai asli', 'success');
            }
        });
    });
    
    // Handle tombol simpan
    $('#btnSimpan').on('click', function() {
        saveChanges();
    });
    
    // Handle tombol refresh halaman
    $(window).on('beforeunload', function() {
        if (changedInputs.size > 0) {
            return 'Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?';
        }
    });
});
</script>
@endpush
