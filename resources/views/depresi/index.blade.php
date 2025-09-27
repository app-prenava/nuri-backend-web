@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">
                                <i class="fas fa-brain me-2"></i>
                                Riwayat Prediksi Depresi
                            </h2>
                            <p class="mb-0 opacity-75">Kelola dan pantau hasil prediksi depresi pasien</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-dark px-3 py-2 fs-6">
                                <i class="fas fa-users me-1"></i>
                                {{-- Menggunakan count() untuk data non-paginasi --}}
                                {{ $prediksiList->count() }} Pasien
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <div class="d-flex gap-2">
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar me-2"></i>Filter Tanggal
                            </label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ request('tanggal') }}" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-filter me-2"></i>Filter Hasil
                            </label>
                            <select name="hasil" class="form-control" style="border-radius: 10px;">
                                <option value="">-- Semua --</option>
                                <option value="bergejala" {{ request('hasil') == 'bergejala' ? 'selected' : '' }}>Bergejala Depresi</option>
                                <option value="tidak_bergejala" {{ request('hasil') == 'tidak_bergejala' ? 'selected' : '' }}>Tidak Bergejala Depresi</option>
                            </select>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @if($prediksiList->count() > 0)
                <div class="row mb-3">
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; background: rgba(40, 167, 69, 0.1);">
                                    <i class="fas fa-smile text-success fs-4"></i>
                                </div>
                                <h3 class="mb-1">
                                    @php
                                        $tidakBergejala = 0;
                                        foreach($prediksiList as $data) {
                                            $finalResult = false;
                                            if ($data->hasil_prediksi == 1) {
                                                if ($data->epds && $data->epds->score >= 10) {
                                                    $finalResult = true;
                                                }
                                            } elseif ($data->hasil_prediksi == 0) {
                                                if ($data->epds && $data->epds->score >= 10) {
                                                    $finalResult = true;
                                                }
                                            }
                                            if (!$finalResult) $tidakBergejala++;
                                        }
                                    @endphp
                                    {{ $tidakBergejala }}
                                </h3>
                                <p class="text-muted mb-0">Tidak Bergejala</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; background: rgba(220, 53, 69, 0.1);">
                                    <i class="fas fa-exclamation-circle text-danger fs-4"></i>
                                </div>
                                <h3 class="mb-1">
                                    @php
                                        $resikoTinggi = 0;
                                        foreach($prediksiList as $data) {
                                            if ($data->epds && $data->epds->score >= 10) {
                                                $resikoTinggi++;
                                            }
                                        }
                                    @endphp
                                    {{ $resikoTinggi }}
                                </h3>
                                <p class="text-muted mb-0">Risiko Bergejala Depresi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; background: rgba(13, 202, 240, 0.1);">
                                    <i class="fas fa-chart-line text-info fs-4"></i>
                                </div>
                                <h3 class="mb-1">
                                    @php
                                        $avgScore = $prediksiList->whereNotNull('epds')->avg('epds.score');
                                    @endphp
                                    {{ number_format($avgScore, 1) }}
                                </h3>
                                <p class="text-muted mb-0">Rata-rata Skor EPDS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2 text-primary"></i>
                                Data Prediksi Depresi Pasien
                            </h5>
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text border-0 bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-0 bg-light" placeholder="Cari nama pasien..." id="searchInput">
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="depresiTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-3 px-4">
                                            <i class="fas fa-user me-2"></i>Pasien
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-brain me-2"></i>Hasil Prediksi
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-clipboard-list me-2"></i>Skor EPDS
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-symptoms me-2"></i>Gejala
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-calendar me-2"></i>Tanggal
                                        </th>
                                        <th class="border-0 py-3 text-center">
                                            <i class="fas fa-cogs me-2"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prediksiList as $data)
                                    <tr class="border-bottom">
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); color: white;">
                                                    {{ strtoupper(substr($data->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $data->user->name ?? '-' }}</h6>
                                                    <small class="text-muted">{{ $data->user->usia ?? '-' }} tahun</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $finalResult = false;
                                                $hasilText = 'Tidak Bergejala Depresi';
                                                $badgeClass = 'bg-success';
                                                
                                                if ($data->hasil_prediksi == 1) {
                                                    if ($data->epds && $data->epds->score >= 10) {
                                                        $finalResult = true;
                                                        $hasilText = 'Risiko Bergejala Depresi';
                                                        $badgeClass = 'bg-danger';
                                                    } else {
                                                        $finalResult = false;
                                                        $hasilText = 'Tidak Bergejala Depresi';
                                                        $badgeClass = 'bg-success';
                                                    }
                                                } elseif ($data->hasil_prediksi == 0) {
                                                    if ($data->epds && $data->epds->score >= 10) {
                                                        $finalResult = true;
                                                        $hasilText = 'Risiko Bergejala Depresi';
                                                        $badgeClass = 'bg-danger';
                                                    } else {
                                                        $finalResult = false;
                                                        $hasilText = 'Tidak Bergejala Depresi';
                                                        $badgeClass = 'bg-success';
                                                    }
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 20px;">
                                                @if($badgeClass == 'bg-success')
                                                    <i class="fas fa-check me-1"></i>
                                                @elseif($badgeClass == 'bg-warning')
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                @else
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                @endif
                                                {{ $hasilText }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge px-3 py-2 
                                                @if($data->epds && $data->epds->score >= 10) bg-danger
                                                @else bg-success
                                                @endif" style="border-radius: 20px;">
                                                {{ $data->epds?->score ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <div class="small">
                                                <div class="mb-1">
                                                    <span class="badge bg-light text-dark me-1">Sedih:</span>
                                                    <span class="fw-semibold">{{ $data->getMerasaSedihTextAttribute() }}</span>
                                                </div>
                                                <div class="mb-1">
                                                    <span class="badge bg-light text-dark me-1">Tersinggung:</span>
                                                    <span class="fw-semibold">{{ $data->getMudahTersinggungTextAttribute() }}</span>
                                                </div>
                                                <div class="mb-1">
                                                    <span class="badge bg-light text-dark me-1">Tidur:</span>
                                                    <span class="fw-semibold">{{ $data->getMasalahTidurTextAttribute() }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div>
                                                <div class="fw-bold">{{ $data->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $data->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('depresi.show', $data->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" style="border-radius: 8px 0 0 8px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $data->id }}"
                                                        style="border-radius: 0 8px 8px 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <div class="modal fade" id="deleteModal{{ $data->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                        Konfirmasi Hapus
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body pt-0">
                                                    <p class="mb-0">Apakah Anda yakin ingin menghapus data prediksi untuk <strong>"{{ $data->user->name ?? 'User' }}"</strong>?</p>
                                                    <small class="text-muted">Data yang dihapus tidak dapat dikembalikan.</small>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('depresi.destroy', $data->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    {{-- BLOK PAGINASI DIHAPUS --}}
                    
                </div>
            @else
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="fas fa-brain text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h4 class="mb-3">Belum Ada Data Prediksi Depresi</h4>
                        <p class="text-muted mb-4">Mulai prediksi depresi pertama untuk pasien Anda</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Konfirmasi Hapus Semua Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>SEMUA</strong> data prediksi depresi?</p>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover effects for the main CTA button */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    }
    
    /* Table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transform: scale(1.002);
        transition: all 0.2s ease;
    }
    
    /* Search functionality */
    #searchInput {
        border-radius: 0 8px 8px 0 !important;
    }
    
    /* Smooth transitions */
    .card, .btn, .badge {
        transition: all 0.3s ease;
    }
    
    /* Custom gradient hover for primary button */
    a[href*="depresi.create"]:hover {
        background: linear-gradient(45deg, #20c997, #28a745) !important;
    }
    
    /* Hapus semua style yang berhubungan dengan paginasi */

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('depresiTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[0];
                if (nameCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    if (nameText.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });
    }
});
</script>
@endsection
