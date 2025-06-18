@extends('layouts.app')

@section('content')
    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Penilaian Alternatif</h3>
        <div>
            <button type="button" id="btnReset" class="btn btn-outline-secondary me-2">
                <i class="fas fa-undo me-1"></i> Reset
            </button>
            <button type="button" id="btnSimpan" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 200px;">Alternatif</th>
                            @foreach($kriterias as $kriteria)
                                <th class="text-center">
                                    {{ $kriteria->kode_kriteria }}
                                    <br>
                                    <small class="text-muted">{{ $kriteria->nama_kriteria }}</small>
                                    <br>
                                    <small class="badge bg-{{ $kriteria->sifat == 'benefit' ? 'success' : 'danger' }}">
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
        sessionStorage.setItem(`penilaian_${inputId}`, value);
        
        // Tandai sebagai berubah
        if (value !== originalValue) {
            changedInputs.add(inputId);
            input.addClass('is-changed');
            // Tambahkan style langsung ke elemen
            input.css({
                'background-color': '#fff3cd',
                'border': '2px solid #ffc107',
                'font-weight': '500',
                'color': '#000'
            });
            console.log('Menandai input berubah:', inputId);
        } else {
            changedInputs.delete(inputId);
            input.removeClass('is-changed');
            // Hapus style inline
            input.css({
                'background-color': '',
                'border': '',
                'font-weight': '',
                'color': ''
            });
        }
        
        updateSaveButton();
    }

    // Fungsi untuk memuat nilai dari session storage
    function loadFromSession() {
        $('.nilai-input').each(function() {
            const input = $(this);
            const inputId = `${input.data('alternatif-id')}-${input.data('kriteria-id')}`;
            const savedValue = sessionStorage.getItem(`penilaian_${inputId}`);
            
            if (savedValue !== null) {
                input.val(savedValue);
                changedInputs.add(inputId);
                input.addClass('is-changed');
            }
        });
        
        updateSaveButton();
    }
    
    // Inisialisasi data original value
    $('.nilai-input').each(function() {
        const input = $(this);
        input.attr('data-original-value', input.val() || '');
        
        // Tambahkan event listener untuk input
        input.on('input', function() {
            saveToSession($(this));
        });
    });
    
    // Muat data dari session storage jika ada
    loadFromSession();
    
    // Tombol reset
    $('#btnReset').on('click', function() {
        if (confirm('Yakin ingin mereset semua perubahan yang belum disimpan?')) {
            sessionStorage.clear();
            location.reload();
        }
    });
    
    // Tangkap klik tombol simpan
    $('#btnSimpan').on('click', function() {
        saveChanges();
    });

    // Fungsi untuk mengirim data ke server
    function saveChanges() {
        if (isSaving || changedInputs.size === 0) return;
        
        const $btn = $('#btnSimpan');
        const updates = [];
        
        // Kumpulkan semua perubahan dari session storage
        changedInputs.forEach(inputId => {
            const [alternatifId, kriteriaId] = inputId.split('-');
            const value = sessionStorage.getItem(`penilaian_${inputId}`);
            
            updates.push({
                alternatif_id: parseInt(alternatifId),
                kriteria_id: parseInt(kriteriaId),
                nilai: value === '' ? null : parseFloat(value)
            });
        });
        
        // Sembunyikan data sensitif dari console log
        const logUpdates = updates.map(update => ({
            alternatif_id: '***',
            kriteria_id: '***',
            nilai: update.nilai
        }));
        console.log('Mengirim data ke server:', logUpdates);
        isSaving = true;
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        // Kirim data ke server
        $.ajax({
            url: '{{ route("penilaian.store") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                updates: updates
            },
            success: function(response) {
                // Sembunyikan data sensitif dari console log
                const logResponse = response.success 
                    ? { success: true, saved_count: response.saved_count }
                    : { success: false, message: response.message };
                console.log('Response:', logResponse);
                if (response.success) {
                    // Perbarui nilai original dan reset style
                    $('.nilai-input.is-changed').each(function() {
                        const input = $(this);
                        input.attr('data-original-value', input.val() || '');
                        input.removeClass('is-changed');
                        // Reset style inline
                        input.css({
                            'background-color': '',
                            'border': '',
                            'font-weight': '',
                            'color': ''
                        });
                    });
                    
                    // Hapus data dari session storage
                    sessionStorage.clear();
                    changedInputs.clear();
                    
                    // Tampilkan notifikasi sukses
                    showAlert('success', `Berhasil menyimpan ${response.saved_count || 0} data`);
                } else {
                    showAlert('danger', response.message || 'Gagal menyimpan data');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                isSaving = false;
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Perubahan');
                updateSaveButton();
            }
        });
    }

    // Fungsi untuk menampilkan notifikasi
    function showAlert(type, message) {
        const alertId = 'alert-' + Date.now();
        const alert = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        
        // Hapus alert yang ada terlebih dahulu
        $('.alert').alert('close');
        
        // Tambahkan alert baru ke container
        $('#alertContainer').html(alert);
        
        // Auto-hide alert setelah 5 detik
        setTimeout(() => {
            $(`#${alertId}`).alert('close');
        }, 5000);
    }
});
</script>
@endpush
