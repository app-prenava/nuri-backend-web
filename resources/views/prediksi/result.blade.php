@extends('layouts.app')

@section('content')
@php
    $method = strtolower($prediction->metode_persalinan ?? '');
    $isCaesar = $method === 'caesar';
    $headlineClass = $isCaesar ? 'text-danger' : 'text-primary';
    $ovalClass     = $isCaesar ? 'border-danger text-danger' : 'border-primary text-primary';

    // Solusi error: cek benar-benar nullable, jangan lempar object Optional
    $hplRaw = $prediction->hpl && $prediction->hpl->hpl ? $prediction->hpl->hpl : null;
    $hpl = $hplRaw
        ? \Carbon\Carbon::parse($hplRaw)->translatedFormat('d F Y')
        : '-';

    $tanggalPrediksi = $prediction->created_at
        ? \Carbon\Carbon::parse($prediction->created_at)->format('d M Y, H:i')
        : '-';
@endphp

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm fade-in" style="background: linear-gradient(135deg, #4dbaff 0%, #1a87e3 100%); border-radius: 15px;">
                <div class="card-body text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-2">
                            <i class="fas fa-baby me-2"></i>
                            Hasil Prediksi Persalinan
                        </h2>
                        <span class="opacity-75">Lihat hasil prediksi dan saran berdasarkan data ibu.</span>
                    </div>
                    <a href="{{ route('prediksi.index') }}" class="btn btn-light d-flex align-items-center shadow-sm px-4 rounded-3 fw-semibold" style="background: #fff; color: #1976d2; border: none;">
                        <i class="fas fa-arrow-left me-2"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Hasil Prediksi -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden fade-in" style="background: linear-gradient(120deg, #f8fbff 75%, #e4f1fe 100%); border-radius: 20px;">
                <div class="card-body p-0">
                    <!-- HEADLINE HASIL -->
                    <div class="d-flex flex-column align-items-center justify-content-center pt-4 pb-1" style="background: #fff; border-radius: 20px 20px 0 0;">
                        <div class="mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow" style="width:75px; height:75px; background: linear-gradient(135deg, #f8fbff, #e1f0fb);">
                                <i class="fas fa-user-md" style="font-size:2.2rem; color:#1a87e3;"></i>
                            </div>
                        </div>
                        <div class="fw-bold {{ $headlineClass }} mt-2" style="font-size:2.6rem;letter-spacing:1.5px;">
                            {{ $isCaesar ? 'Caesar' : 'Normal' }}
                        </div>
                        <div class="border {{ $ovalClass }} rounded-pill px-4 py-2 fw-bold mt-2 mb-1" style="font-size:1.13rem; border-width:2px;">
                            Prediksi Metode Persalinan: <span class="{{ $headlineClass }}">{{ ucfirst($prediction->metode_persalinan) }}</span>
                        </div>
                        <div class="text-muted small mb-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $tanggalPrediksi }} &nbsp;|&nbsp;
                            <b>HPL:</b> {{ $hpl }}
                        </div>
                    </div>

                    <!-- Faktor & Confidence -->
                    <div class="row g-3 justify-content-center px-4 pt-2 pb-1">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="rounded-4 px-3 py-3 h-100 d-flex align-items-center justify-content-center gap-3 shadow-sm" style="background: linear-gradient(90deg, #eaf8ff, #f8fbff 80%);">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;background:#e0f7fa;">
                                    <i class="fas fa-lightbulb text-info" style="font-size:1.4rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-secondary" style="font-size: 1.02rem;">Faktor Penentu</div>
                                    <div style="font-size: 1.07rem;">{{ $prediction->faktor ?? 'Tidak tersedia' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="rounded-4 px-3 py-3 h-100 d-flex align-items-center justify-content-center gap-3 shadow-sm" style="background: linear-gradient(90deg, #eafaf4, #f8fbff 80%);">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;background:#e1fbe8;">
                                    <i class="fas fa-percentage text-success" style="font-size:1.4rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-secondary" style="font-size: 1.02rem;">Confidence</div>
                                    <div style="font-size: 1.19rem;">{{ is_numeric($prediction->confidence) ? round($prediction->confidence) . '%' : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Data Ibu -->
                    <div class="px-4 pt-4 pb-0">
                        <div class="fw-semibold text-dark mb-2" style="font-size:1.09rem;">
                            <i class="fas fa-info-circle text-primary me-2"></i>Data Lengkap Ibu & Kehamilan
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#e3f3ff,#b7e6ff);">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <span><b>Nama Ibu:</b> {{ $prediction->user->name ?? '-' }}</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#ffe1f2,#fde8ed);">
                                        <i class="fas fa-hourglass-half text-primary"></i>
                                    </div>
                                    <span><b>Usia Ibu:</b> {{ $prediction->usia_ibu }} tahun</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#e4fbe6,#d5f1dc);">
                                        <i class="fas fa-tint text-primary"></i>
                                    </div>
                                    <span><b>Tekanan Darah:</b> {{ ucfirst($prediction->tekanan_darah) }}</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#fff7e1,#fff3c6);">
                                        <i class="fas fa-history text-primary"></i>
                                    </div>
                                    <span><b>Riwayat Persalinan:</b> {{ ucfirst($prediction->riwayat_persalinan) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#e5e4fb,#e1eaff);">
                                        <i class="fas fa-baby text-primary"></i>
                                    </div>
                                    <span><b>Posisi Janin:</b> {{ ucfirst($prediction->posisi_janin) }}</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#fbe4e4,#ffe4ee);">
                                        <i class="fas fa-heartbeat text-primary"></i>
                                    </div>
                                    <span><b>Kondisi Janin:</b> {{ ucfirst($prediction->kondisi_kesehatan_janin) }}</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 mb-2 d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#e1fff3,#d6fbe8);">
                                        <i class="fas fa-notes-medical text-primary"></i>
                                    </div>
                                    <span><b>Riwayat Kesehatan Ibu:</b> {{ ucfirst($prediction->riwayat_kesehatan_ibu) }}</span>
                                </div>
                                <div class="rounded-3 border bg-white px-3 py-2 d-flex align-items-center gap-3 d-md-none">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,#f8ffea,#edffe6);">
                                        <i class="fas fa-calendar text-primary"></i>
                                    </div>
                                    <span><b>HPL:</b> {{ $hpl }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="px-4 pb-4 pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" style="background: #fff; border-radius: 0 0 20px 20px;">
                        <a href="{{ route('prediksi.index') }}"
                           class="btn btn-light fw-semibold px-4 py-2 rounded-3 d-flex align-items-center gap-2 border"
                           style="background: #fff; color: #1976d2; border: 1.5px solid #d7e4ef;">
                            <i class="fas fa-list"></i> Kembali ke Riwayat
                        </a>
                        @if(Route::has('prediksi.print'))
                        <a href="{{ route('prediksi.print', $prediction->id) }}" target="_blank"
                           class="btn fw-semibold px-4 py-2 rounded-3 d-flex align-items-center gap-2"
                           style="background: linear-gradient(45deg, #4dbaff, #1a87e3); color: white;">
                            <i class="fas fa-print"></i> Cetak Hasil
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: fadeInMove 0.7s cubic-bezier(0.22,1,0.36,1);
    }
    @keyframes fadeInMove {
        0% { opacity: 0; transform: translateY(16px);}
        100% { opacity: 1; transform: translateY(0);}
    }
    .card, .btn, .border, .rounded-3, .rounded-4 {
        transition: all .25s;
    }
    .btn:hover {
        filter: brightness(1.07) drop-shadow(0 3px 18px #b1e4ff41);
        transform: translateY(-2px);
    }
    @media (max-width: 991px) {
        .rounded-4, .rounded-3 { border-radius: 12px !important; }
        .btn { font-size: 1rem; }
    }
</style>
@endsection
