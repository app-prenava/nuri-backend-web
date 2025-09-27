@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Medical Theme -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">
                                <i class="fas fa-heartbeat me-2"></i>
                                Riwayat Deteksi Kesehatan
                            </h2>
                            <p class="mb-0 opacity-75">Kelola dan pantau hasil deteksi kesehatan pasien</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-dark px-3 py-2 fs-6">
                                <i class="fas fa-users me-1"></i>
                                {{ count($deteksiPenyakit) }} Pasien
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <!-- Primary CTA Button - Deteksi Baru -->
                <div>
                    <a href="{{ route('deteksi.create') }}" class="btn btn-lg px-4 py-3"
                       style="background: linear-gradient(45deg, #28a745, #20c997); border: none; color: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-plus-circle me-2 fs-5"></i>
                        <span class="fw-bold">Mulai Deteksi Baru</span>
                        <small class="d-block mt-1 opacity-75">Periksa pasien baru</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            @if(count($deteksiPenyakit) > 0)
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; background: rgba(40, 167, 69, 0.1);">
                                    <i class="fas fa-user-check text-success fs-4"></i>
                                </div>
                                <h3 class="mb-1">{{ $deteksiPenyakit->where('diabetes_prediction', 0)->where('hypertension_prediction', 0)->count() }}</h3>
                                <p class="text-muted mb-0">Pasien Sehat</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; background: rgba(255, 193, 7, 0.1);">
                                    <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                                </div>
                                <h3 class="mb-1">{{ $deteksiPenyakit->where('diabetes_prediction', 1)->count() }}</h3>
                                <p class="text-muted mb-0">Diabetes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; background: rgba(220, 53, 69, 0.1);">
                                    <i class="fas fa-heart text-danger fs-4"></i>
                                </div>
                                <h3 class="mb-1">{{ $deteksiPenyakit->where('hypertension_prediction', 1)->count() }}</h3>
                                <p class="text-muted mb-0">Hipertensi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-body text-center">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px; background: rgba(13, 202, 240, 0.1);">
                                    <i class="fas fa-baby text-info fs-4"></i>
                                </div>
                                <h3 class="mb-1">{{ $deteksiPenyakit->where('maternal_health_prediction', '!=', 'low risk')->count() }}</h3>
                                <p class="text-muted mb-0">Risiko Maternal</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2 text-primary"></i>
                                Data Deteksi Pasien
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
                            <table class="table table-hover mb-0" id="deteksiTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-3 px-4">
                                            <i class="fas fa-user me-2"></i>Pasien
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-calculator me-2"></i>BMI
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-tint me-2"></i>Diabetes
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-heart me-2"></i>Hipertensi
                                        </th>
                                        <th class="border-0 py-3">
                                            <i class="fas fa-baby me-2"></i>Kesehatan Maternal
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
                                    @foreach($deteksiPenyakit as $deteksi)
                                    <tr class="border-bottom">
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); color: white;">
                                                    {{ strtoupper(substr($deteksi->nama, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $deteksi->nama }}</h6>
                                                    <small class="text-muted">{{ $deteksi->age }} tahun</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge px-3 py-2
                                                @if($deteksi->bmi < 18.5) bg-warning
                                                @elseif($deteksi->bmi >= 18.5 && $deteksi->bmi < 25) bg-success
                                                @elseif($deteksi->bmi >= 25 && $deteksi->bmi < 30) bg-warning
                                                @else bg-danger
                                                @endif" style="border-radius: 20px;">
                                                {{ number_format($deteksi->bmi, 1) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @if($deteksi->diabetes_prediction == 1)
                                                <span class="badge bg-danger px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Positif
                                                </span>
                                            @else
                                                <span class="badge bg-success px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-check me-1"></i>Negatif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($deteksi->hypertension_prediction == 1)
                                                <span class="badge bg-danger px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Positif
                                                </span>
                                            @else
                                                <span class="badge bg-success px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-check me-1"></i>Negatif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($deteksi->maternal_health_prediction == 'low risk')
                                                <span class="badge bg-success px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-check me-1"></i>Risiko Rendah
                                                </span>
                                            @elseif($deteksi->maternal_health_prediction == 'medium risk')
                                                <span class="badge bg-warning px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Risiko Sedang
                                                </span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Risiko Tinggi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div>
                                                <div class="fw-bold">{{ $deteksi->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $deteksi->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('deteksi.show', $deteksi->deteksi_id) }}"
                                                   class="btn btn-sm btn-outline-primary" style="border-radius: 8px 0 0 8px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $deteksi->deteksi_id }}"
                                                        style="border-radius: 0 8px 8px 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $deteksi->deteksi_id }}" tabindex="-1">
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
                                                    <p class="mb-0">Apakah Anda yakin ingin menghapus data deteksi untuk <strong>"{{ $deteksi->nama }}"</strong>?</p>
                                                    <small class="text-muted">Data yang dihapus tidak dapat dikembalikan.</small>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('deteksi.destroy', $deteksi->deteksi_id) }}" method="POST" class="d-inline">
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
                </div>
            @else
                <!-- Empty State -->
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="fas fa-heartbeat text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h4 class="mb-3">Belum Ada Data Deteksi</h4>
                        <p class="text-muted mb-4">Mulai deteksi kesehatan pertama untuk pasien Anda</p>
                        <a href="{{ route('deteksi.create') }}" class="btn btn-lg px-5 py-3"
                           style="background: linear-gradient(45deg, #28a745, #20c997); border: none; color: white; border-radius: 15px;">
                            <i class="fas fa-plus-circle me-2"></i>
                            <span class="fw-bold">Mulai Deteksi Pertama</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete All Modal -->
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
                <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>SEMUA</strong> data deteksi?</p>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <a href="{{ route('deteksi.deleteAll') }}" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i>Ya, Hapus Semua
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
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
    a[href*="deteksi.create"]:hover {
        background: linear-gradient(45deg, #20c997, #28a745) !important;
    }
</style>

<!-- JavaScript for Search Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('deteksiTable');

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
