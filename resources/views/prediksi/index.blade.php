@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">
                                <i class="fas fa-baby me-2"></i>
                                Riwayat Prediksi Persalinan
                            </h2>
                            <p class="mb-0 opacity-75">Kelola dan pantau hasil prediksi metode persalinan pasien</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-dark px-3 py-2 fs-6">
                                <i class="fas fa-users me-1"></i>
                                {{ $users->where('role','ibu_hamil')->count() }} Pasien
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions: Tambah, Hapus Semua, Export -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <div>
                    <a href="{{ route('prediksi.form') }}" class="btn btn-lg px-4 py-3 fw-bold d-flex align-items-center gap-2"
                       style="background: linear-gradient(45deg, #28a745, #20c997); border: none; color: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);">
                        <i class="fas fa-plus-circle fs-5"></i>
                        Tambah Prediksi Baru
                        <small class="d-block mt-1 opacity-75 fw-normal" style="font-size:0.98rem;">Input prediksi baru</small>
                    </a>
                </div>
                <div class="d-flex gap-2">
                    @if($allPredictions->count() > 0)
                        <!-- Hapus Semua pakai modal -->
                        <button type="button" class="btn btn-outline-danger btn-lg d-flex align-items-center gap-2 fw-semibold"
                                style="border-radius: 12px;"
                                data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                            <i class="fas fa-trash-alt"></i> Hapus Semua
                        </button>
                        <button class="btn btn-outline-primary btn-lg d-flex align-items-center gap-2 fw-semibold" style="border-radius: 12px;" disabled>
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ALERT MESSAGE -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #e3f8ff;">
                        <i class="fas fa-baby text-info fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $allPredictions->filter(fn($p) => strtolower($p->metode_persalinan) == 'normal')->count() }}</h3>
                    <p class="text-muted mb-0">Prediksi Normal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #ffe3ea;">
                        <i class="fas fa-baby text-danger fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $allPredictions->filter(fn($p) => strtolower($p->metode_persalinan) == 'caesar')->count() }}</h3>
                    <p class="text-muted mb-0">Prediksi Caesar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #e4eaff;">
                        <i class="fas fa-users text-primary fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $users->where('role','ibu_hamil')->count() }}</h3>
                    <p class="text-muted mb-0">Total Ibu Hamil</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #e3ffec;">
                        <i class="fas fa-database text-success fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $allPredictions->count() }}</h3>
                    <p class="text-muted mb-0">Total Prediksi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('prediksi.index') }}" class="row g-2 align-items-end">
                @if(Auth::user()->role === 'bidan')
                <div class="col-md-3">
                    <label for="user_id" class="form-label fw-semibold mb-1"><i class="fas fa-user me-1"></i>Nama Ibu Hamil</label>
                    <select name="user_id" id="user_id" class="form-select shadow-sm rounded-3">
                        <option value="">Semua</option>
                        @foreach($users as $user)
                            @if($user->role === 'ibu_hamil')
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <label for="method" class="form-label fw-semibold mb-1"><i class="fas fa-filter me-1"></i>Metode Persalinan</label>
                    <select name="method" id="method" class="form-select shadow-sm rounded-3">
                        <option value="">Semua</option>
                        <option value="normal" {{ request('method') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="caesar" {{ request('method') == 'caesar' ? 'selected' : '' }}>Caesar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="hpl" class="form-label fw-semibold mb-1"><i class="fas fa-calendar me-1"></i>HPL</label>
                    <input type="date" name="hpl" id="hpl" class="form-control shadow-sm rounded-3" value="{{ request('hpl') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1"><i class="fas fa-search me-1"></i>Cari Nama</label>
                    <input type="text" name="search" class="form-control shadow-sm rounded-3" placeholder="Cari nama pasien..." value="{{ request('search') }}">
                </div>
                <div class="col-auto mt-md-4 mt-3">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-3 px-4">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-auto mt-md-4 mt-3">
                    <a href="{{ route('prediksi.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm rounded-3 px-4">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-4" style="border-radius: 15px 15px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-table me-2 text-primary"></i>Data Prediksi Metode Persalinan Pasien</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="border-radius: 12px; overflow: hidden;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4"><i class="fas fa-user me-2"></i>Pasien</th>
                                    <th class="border-0 py-3"><i class="fas fa-stethoscope me-2"></i>Metode</th>
                                    <th class="border-0 py-3"><i class="fas fa-balance-scale me-2"></i>Faktor</th>
                                    <th class="border-0 py-3"><i class="fas fa-percentage me-2"></i>Confidence</th>
                                    <th class="border-0 py-3"><i class="fas fa-calendar me-2"></i>HPL</th>
                                    <th class="border-0 py-3"><i class="fas fa-clock me-2"></i>Waktu Prediksi</th>
                                    <th class="border-0 py-3 text-center"><i class="fas fa-cogs me-2"></i>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($predictions as $prediction)
                                    <tr class="border-bottom">
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-size: 1.3rem;">
                                                    {{ strtoupper(substr($prediction->user->name ?? '', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ $prediction->user->name ?? '-' }}</span>
                                                    <br>
                                                    <span class="text-muted small">{{ $prediction->usia_ibu ?? '-' }} tahun</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-semibold
                                                @if(strtolower($prediction->metode_persalinan) == 'normal') text-info
                                                @elseif(strtolower($prediction->metode_persalinan) == 'caesar') text-danger
                                                @else text-muted @endif">
                                                {{ ucfirst($prediction->metode_persalinan) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            {{ $prediction->faktor ?? '-' }}
                                        </td>
                                        <td class="py-3">
                                            {{ is_numeric($prediction->confidence) ? round($prediction->confidence) . '%' : '-' }}
                                        </td>
                                        <td class="py-3">
                                            {{ $prediction->hpl && $prediction->hpl->hpl
                                                ? \Carbon\Carbon::parse($prediction->hpl->hpl)->format('d-m-Y')
                                                : '-' }}
                                        </td>
                                        <td class="py-3">
                                            <div>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($prediction->created_at)->format('d M Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($prediction->created_at)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('prediksi.show', $prediction->id) }}"
                                                   class="btn btn-sm btn-outline-primary" style="border-radius: 8px 0 0 8px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('prediksi.delete', $prediction->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 0 8px 8px 0;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-baby text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <br>
                                            <span class="d-block mt-2">Belum ada data prediksi.</span>
                                        </td>
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
                <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>SEMUA</strong> data prediksi?</p>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('prediksi.deleteAll') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i>Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.14) !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transform: scale(1.002);
        transition: all 0.2s ease;
    }
    .card, .btn, .badge {
        transition: all 0.3s ease;
    }
    a[href*="prediksi.form"]:hover {
        background: linear-gradient(45deg, #20c997, #28a745) !important;
    }
</style>
@endsection
