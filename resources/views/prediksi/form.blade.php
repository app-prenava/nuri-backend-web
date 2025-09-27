@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-3">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #53b6ec 0%, #58b3fa 100%); border-radius: 18px;">
                <div class="card-body text-white d-flex justify-content-between align-items-center py-4" style="min-height: 70px;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-baby" style="font-size: 2.1rem;"></i>
                        <span style="font-size: 1.7rem; font-weight: 700;">Form Prediksi Persalinan</span>
                    </div>
                    <a href="{{ route('prediksi.index') }}"
                        class="btn btn-white fw-semibold d-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-none"
                        style="border:1.5px solid #d7e4ef; color:#2176a6; background:#fff; font-size:1.13rem;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert di bawah header -->
    <div class="row mb-3">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="alert alert-info rounded-3 px-4 py-2"
                style="background:linear-gradient(90deg, #eef7ff 70%, #e6f2fb);border:1.5px solid #bae0fd; font-size:1.12rem;">
                <i class="fas fa-info-circle me-2"></i>
                <b>Semua kolom wajib diisi</b>, gunakan kata sederhana, misal: <b>Normal</b> jika sehat.
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
    <div class="row mb-2">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="alert alert-danger shadow-sm rounded-3 mb-3">
                <strong>Mohon lengkapi isian berikut:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    @if(session('error'))
        <div class="row mb-2">
            <div class="col-lg-10 col-md-12 mx-auto">
                <div class="alert alert-danger shadow-sm rounded-3 mb-3">{{ session('error') }}</div>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="row mb-2">
            <div class="col-lg-10 col-md-12 mx-auto">
                <div class="alert alert-success shadow-sm rounded-3 mb-3">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="card shadow-sm border-0" style="border-radius: 22px;">
                <div class="card-body px-5 py-5">
                    <div class="mb-3">
                        <span class="d-block text-primary" style="font-size: 1.15rem; font-weight: 600;">
                            Mohon isi data berikut dengan lengkap dan benar agar hasil prediksi lebih akurat
                        </span>
                    </div>
                    <form action="{{ route('prediksi.store') }}" method="POST" class="needs-validation" novalidate autocomplete="off">
                        @csrf

                        <!-- Nama Ibu Hamil Full Width -->
                        @if(Auth::user()->role === 'bidan')
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold">
                                Nama Ibu Hamil <span class="text-danger">*</span>
                            </label>
                            <select id="user_id" name="user_id" class="form-select custom-input" required>
                                <option value="">-- Pilih Ibu Hamil --</option>
                                @foreach($users as $user)
                                    @if($user->role === 'ibu_hamil')
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger small mt-1">Nama ibu hamil wajib dipilih.</div>
                            @enderror
                        </div>
                        @else
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        @endif

                        <div class="row gx-3 gy-3">
                            <div class="col-md-6">
                                <label for="usia_ibu" class="form-label fw-semibold">
                                    Usia Ibu <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="usia_ibu" name="usia_ibu" class="form-control custom-input" min="15" max="50"
                                    value="{{ old('usia_ibu') }}" required placeholder="Isi usia ibu, contoh: 28">
                                <div class="text-muted mt-1" style="font-size: 1rem;">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Masukkan usia antara 15 sampai 50 tahun
                                </div>
                                @error('usia_ibu')
                                    <div class="text-danger small mt-1">{{ $message == "The usia ibu field is required." ? "Usia ibu belum diisi." : $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tekanan_darah" class="form-label fw-semibold">
                                    Tekanan Darah <span class="text-danger">*</span>
                                </label>
                                <select id="tekanan_darah" name="tekanan_darah" class="form-select custom-input" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="normal" {{ old('tekanan_darah') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="rendah" {{ old('tekanan_darah') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="tinggi" {{ old('tekanan_darah') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                </select>
                                @error('tekanan_darah')
                                    <div class="text-danger small mt-1">Tekanan darah wajib dipilih.</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="riwayat_persalinan" class="form-label fw-semibold">
                                    Riwayat Persalinan <span class="text-danger">*</span>
                                </label>
                                <select id="riwayat_persalinan" name="riwayat_persalinan" class="form-select custom-input" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="tidak ada" {{ old('riwayat_persalinan') == 'tidak ada' ? 'selected' : '' }}>Tidak Ada</option>
                                    <option value="normal" {{ old('riwayat_persalinan') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="caesar" {{ old('riwayat_persalinan') == 'caesar' ? 'selected' : '' }}>Caesar</option>
                                </select>
                                @error('riwayat_persalinan')
                                    <div class="text-danger small mt-1">Riwayat persalinan wajib dipilih.</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="posisi_janin" class="form-label fw-semibold">
                                    Posisi Janin <span class="text-danger">*</span>
                                </label>
                                <select id="posisi_janin" name="posisi_janin" class="form-select custom-input" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="normal" {{ old('posisi_janin') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="lintang" {{ old('posisi_janin') == 'lintang' ? 'selected' : '' }}>Lintang</option>
                                    <option value="sungsang" {{ old('posisi_janin') == 'sungsang' ? 'selected' : '' }}>Sungsang</option>
                                </select>
                                @error('posisi_janin')
                                    <div class="text-danger small mt-1">Posisi janin wajib dipilih.</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="riwayat_kesehatan_ibu" class="form-label fw-semibold">
                                    Riwayat Kesehatan Ibu <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="riwayat_kesehatan_ibu" name="riwayat_kesehatan_ibu" class="form-control custom-input"
                                    value="{{ old('riwayat_kesehatan_ibu') }}" required placeholder="Contoh: Hipertensi, Mata Minus">
                                @error('riwayat_kesehatan_ibu')
                                    <div class="text-danger small mt-1">Riwayat kesehatan ibu wajib diisi.</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kondisi_kesehatan_janin" class="form-label fw-semibold">
                                    Kondisi Kesehatan Janin <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="kondisi_kesehatan_janin" name="kondisi_kesehatan_janin" class="form-control custom-input"
                                    value="{{ old('kondisi_kesehatan_janin') }}" required placeholder="Contoh: Fetal Distress, Bayi Besar">
                                @error('kondisi_kesehatan_janin')
                                    <div class="text-danger small mt-1">Kondisi janin wajib diisi.</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-gradient-main px-5 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm rounded-3"
                                style="font-size: 1.22rem;">
                                <i class="fas fa-check-circle me-2"></i>
                                Prediksi Sekarang
                            </button>
                        </div>
                    </form>

                    <!-- ALERT KUNING di dalam card-body, di bawah tombol submit -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-warning mb-0 rounded-3 px-4 py-2"
                                style="background: #fffbe9; border:1.5px solid #ffe7a7; font-size:1.12rem;">
                                <span class="fw-bold d-block mb-1" style="color:#d29b00;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                                </span>
                                <div class="ms-4" style="color:#222; font-size: 1.03rem;">
                                    <i class="fas fa-info-circle me-1" style="color:#a2a2a2;font-size:0.99rem;"></i>
                                    Hasil prediksi metode persalinan ini hanya untuk referensi awal dan tidak menggantikan konsultasi medis profesional.
                                    Pastikan semua data yang dimasukkan sesuai untuk hasil yang optimal.
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END ALERT -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-input, .custom-input:focus {
        border: 1.5px solid #e0e6ed !important;
        box-shadow: none !important;
        background: #f9fcff !important;
        color: #22364b;
        transition: border 0.2s, background 0.2s;
        font-size: 1.13rem !important;
        min-height: 48px;
    }
    .form-label { font-weight: 600; font-size: 1.13rem;}
    .btn-gradient-main {
        background: linear-gradient(45deg, #4dbaff, #1a87e3);
        color: white !important;
        border: none;
        font-size: 1.22rem !important;
        box-shadow: 0 2px 16px rgba(30,170,255,0.10);
        letter-spacing: 0.5px;
        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }
    .btn-gradient-main:hover {
        background: linear-gradient(60deg, #1a87e3, #4dbaff);
        color: #fff !important;
    }
    .btn-white {
        background: #fff !important;
        color: #2176a6 !important;
        border: 1.5px solid #d7e4ef !important;
        font-weight: 600;
        font-size: 1.13rem;
    }
    .needs-validation .form-control:invalid,
    .needs-validation .form-select:invalid {
        border-color: #e0e6ed !important;
        background: #fff5f5 !important;
    }
</style>
@endsection
