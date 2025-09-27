@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Ibu Hamil</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('bidan.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Detail Ibu Hamil</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bidan.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Cetak
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Profile Summary Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Profil Ibu Hamil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $ibuHamil->name ?? 'Nama tidak tersedia' }}</h4>
                    <p class="text-muted mb-3">{{ $ibuHamil->usia ?? '-' }} tahun</p>
                    
                    @if($ibuHamil->usia_kehamilan)
                        <div class="alert alert-info">
                            <i class="fas fa-baby me-2"></i>
                            <strong>Usia Kehamilan:</strong> {{ $ibuHamil->usia_kehamilan }} minggu
                        </div>
                    @endif

                    @if($ibuHamil->saldo_total)
                        <div class="alert alert-success">
                            <i class="fas fa-wallet me-2"></i>
                            <strong>Saldo Total:</strong> Rp {{ number_format($ibuHamil->saldo_total, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="col-xl-8 col-lg-7">
            <!-- Personal Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-id-card me-2"></i>Informasi Pribadi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                            <p class="mb-0">{{ $ibuHamil->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Tanggal Lahir</label>
                            <p class="mb-0">
                                @if($ibuHamil->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($ibuHamil->tanggal_lahir)->format('d F Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Usia</label>
                            <p class="mb-0">{{ $ibuHamil->usia ?? '-' }} tahun</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Nomor Telepon</label>
                            <p class="mb-0">
                                @if($ibuHamil->nomor_telepon)
                                    <a href="tel:{{ $ibuHamil->nomor_telepon }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>{{ $ibuHamil->nomor_telepon }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted">Alamat</label>
                            <p class="mb-0">{{ $ibuHamil->alamat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Pendidikan Terakhir</label>
                            <p class="mb-0">{{ $ibuHamil->pendidikan_terakhir ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Pekerjaan</label>
                            <p class="mb-0">{{ $ibuHamil->pekerjaan ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Golongan Darah</label>
                            <p class="mb-0">
                                @if($ibuHamil->golongan_darah)
                                    <span class="badge bg-info">{{ $ibuHamil->golongan_darah }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Husband Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-male me-2"></i>Informasi Suami
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Nama Suami</label>
                            <p class="mb-0">{{ $ibuHamil->nama_suami ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Usia Suami</label>
                            <p class="mb-0">{{ $ibuHamil->usia_suami ?? '-' }} tahun</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Telepon Suami</label>
                            <p class="mb-0">
                                @if($ibuHamil->telepon_suami)
                                    <a href="tel:{{ $ibuHamil->telepon_suami }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>{{ $ibuHamil->telepon_suami }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Pekerjaan Suami</label>
                            <p class="mb-0">{{ $ibuHamil->pekerjaan_suami ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pregnancy Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-baby me-2"></i>Informasi Kehamilan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Usia Kehamilan</label>
                            <p class="mb-0">
                                @if($ibuHamil->usia_kehamilan)
                                    <span class="badge bg-success fs-6">{{ $ibuHamil->usia_kehamilan }} minggu</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Trimester</label>
                            <p class="mb-0">
                                @if($ibuHamil->usia_kehamilan)
                                    @php
                                        $trimester = $ibuHamil->usia_kehamilan <= 12 ? '1' : 
                                                     ($ibuHamil->usia_kehamilan <= 28 ? '2' : '3');
                                    @endphp
                                    <span class="badge bg-info">Trimester {{ $trimester }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        @if($ibuHamil->usia_kehamilan)
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted">Perkiraan Lahir</label>
                                <p class="mb-0">
                                    @php
                                        $perkiraanLahir = now()->addWeeks(40 - $ibuHamil->usia_kehamilan);
                                    @endphp
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $perkiraanLahir->format('d F Y') }}
                                    <small class="text-muted">({{ $perkiraanLahir->diffForHumans() }})</small>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            @if($ibuHamil->saldo_total)
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Informasi Keuangan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Saldo Total</label>
                            <p class="mb-0">
                                <span class="h5 text-success">
                                    Rp {{ number_format($ibuHamil->saldo_total, 0, ',', '.') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Status</label>
                            <p class="mb-0">
                                <span class="badge {{ $ibuHamil->saldo_total > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $ibuHamil->saldo_total > 0 ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, nav {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    }
    
    .card {
        border: none;
        border-radius: 0.75rem;
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .card-header {
        border-radius: 0.75rem 0.75rem 0 0 !important;
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }
    
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.5em 0.75em;
    }
    
    .alert {
        border-radius: 0.5rem;
        border: none;
    }
</style>
@endpush
@endsection