@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register Bidan') }}</div>

                <div class="card-body">
                    {{-- Alert untuk error umum --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Alert untuk success --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle"></i> Berhasil!</strong>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.bidan') }}" novalidate>
                        @csrf

                        {{-- Name Field --}}
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">
                                {{ __('Name') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="name" 
                                       type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="Masukkan nama lengkap"
                                       maxlength="255">
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Maksimal 255 karakter</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email Field --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                {{ __('Email Address') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="contoh@email.com"
                                       maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Masukkan email yang valid dan belum terdaftar</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Field --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">
                                {{ __('Password') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Minimal 8 karakter"
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Minimal 8 karakter, kombinasi huruf dan angka direkomendasikan</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm Password Field --}}
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">
                                {{ __('Confirm Password') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="password-confirm" 
                                       type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Ulangi password"
                                       minlength="8">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Harus sama dengan password di atas</div>
                                @enderror
                            </div>
                        </div>

                        {{-- BIDAN Specific Fields --}}
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-user-md"></i> Informasi Bidan</h5>

                        {{-- NIK Field --}}
                        <div class="row mb-3">
                            <label for="nik" class="col-md-4 col-form-label text-md-end">
                                {{ __('NIK') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="nik" 
                                       type="text" 
                                       class="form-control @error('nik') is-invalid @enderror" 
                                       name="nik" 
                                       value="{{ old('nik') }}" 
                                       required
                                       placeholder="16 digit NIK"
                                       maxlength="16"
                                       pattern="[0-9]{16}"
                                       inputmode="numeric">
                                @error('nik')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">16 digit angka sesuai KTP</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nomor STR Field --}}
                        <div class="row mb-3">
                            <label for="nomor_str" class="col-md-4 col-form-label text-md-end">
                                {{ __('Nomor STR') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="nomor_str" 
                                       type="text" 
                                       class="form-control @error('nomor_str') is-invalid @enderror" 
                                       name="nomor_str" 
                                       value="{{ old('nomor_str') }}" 
                                       required
                                       placeholder="Nomor Surat Tanda Registrasi"
                                       maxlength="20">
                                @error('nomor_str')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Nomor STR yang masih berlaku</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Masa Berlaku STR Field --}}
                        <div class="row mb-3">
                            <label for="masa_berlaku_str" class="col-md-4 col-form-label text-md-end">
                                {{ __('Masa Berlaku STR') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="masa_berlaku_str" 
                                       type="date" 
                                       class="form-control @error('masa_berlaku_str') is-invalid @enderror" 
                                       name="masa_berlaku_str" 
                                       value="{{ old('masa_berlaku_str') }}" 
                                       required
                                       min="{{ date('Y-m-d') }}">
                                @error('masa_berlaku_str')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Tanggal berlaku STR (tidak boleh masa lalu)</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nomor SIPB Field --}}
                        <div class="row mb-3">
                            <label for="nomor_sipb" class="col-md-4 col-form-label text-md-end">
                                {{ __('Nomor SIPB') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="nomor_sipb" 
                                       type="text" 
                                       class="form-control @error('nomor_sipb') is-invalid @enderror" 
                                       name="nomor_sipb" 
                                       value="{{ old('nomor_sipb') }}" 
                                       required
                                       placeholder="Nomor Surat Izin Praktik Bidan"
                                       maxlength="20">
                                @error('nomor_sipb')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Nomor SIPB yang masih berlaku</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Masa Berlaku SIPB Field --}}
                        <div class="row mb-3">
                            <label for="masa_berlaku_sipb" class="col-md-4 col-form-label text-md-end">
                                {{ __('Masa Berlaku SIPB') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="masa_berlaku_sipb" 
                                       type="date" 
                                       class="form-control @error('masa_berlaku_sipb') is-invalid @enderror" 
                                       name="masa_berlaku_sipb" 
                                       value="{{ old('masa_berlaku_sipb') }}" 
                                       required
                                       min="{{ date('Y-m-d') }}">
                                @error('masa_berlaku_sipb')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Tanggal berlaku SIPB (tidak boleh masa lalu)</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Spesialisasi Field --}}
                        <div class="row mb-3">
                            <label for="spesialisasi" class="col-md-4 col-form-label text-md-end">
                                {{ __('Spesialisasi') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="spesialisasi" 
                                        class="form-select @error('spesialisasi') is-invalid @enderror" 
                                        name="spesialisasi" 
                                        required>
                                    <option value="">Pilih Spesialisasi</option>
                                    <option value="Bidan Umum" {{ old('spesialisasi') == 'Bidan Umum' ? 'selected' : '' }}>Bidan Umum</option>
                                    <option value="Bidan Komunitas" {{ old('spesialisasi') == 'Bidan Komunitas' ? 'selected' : '' }}>Bidan Komunitas</option>
                                    <option value="Bidan Klinik" {{ old('spesialisasi') == 'Bidan Klinik' ? 'selected' : '' }}>Bidan Klinik</option>
                                    <option value="Bidan Rumah Sakit" {{ old('spesialisasi') == 'Bidan Rumah Sakit' ? 'selected' : '' }}>Bidan Rumah Sakit</option>
                                    <option value="Lainnya" {{ old('spesialisasi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('spesialisasi')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Pilih bidang spesialisasi</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tempat Praktik Field --}}
                        <div class="row mb-3">
                            <label for="tempat_praktik" class="col-md-4 col-form-label text-md-end">
                                {{ __('Tempat Praktik') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="tempat_praktik" 
                                       type="text" 
                                       class="form-control @error('tempat_praktik') is-invalid @enderror" 
                                       name="tempat_praktik" 
                                       value="{{ old('tempat_praktik') }}" 
                                       required
                                       placeholder="Nama klinik/rumah sakit/puskesmas"
                                       maxlength="255">
                                @error('tempat_praktik')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Nama tempat praktik utama</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Alamat Praktik Field --}}
                        <div class="row mb-3">
                            <label for="alamat_praktik" class="col-md-4 col-form-label text-md-end">
                                {{ __('Alamat Praktik') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <textarea id="alamat_praktik" 
                                          class="form-control @error('alamat_praktik') is-invalid @enderror" 
                                          name="alamat_praktik" 
                                          required 
                                          rows="3"
                                          placeholder="Alamat lengkap tempat praktik"
                                          maxlength="500">{{ old('alamat_praktik') }}</textarea>
                                @error('alamat_praktik')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Alamat lengkap (maksimal 500 karakter)</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Telepon Tempat Praktik Field --}}
                        <div class="row mb-3">
                            <label for="telepon_tempat_praktik" class="col-md-4 col-form-label text-md-end">
                                {{ __('Telepon Tempat Praktik') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="telepon_tempat_praktik" 
                                       type="tel" 
                                       class="form-control @error('telepon_tempat_praktik') is-invalid @enderror" 
                                       name="telepon_tempat_praktik" 
                                       value="{{ old('telepon_tempat_praktik') }}" 
                                       required
                                       placeholder="08xxxxxxxxxx atau 021xxxxxxxx"
                                       pattern="[0-9+\-\s\(\)]{8,15}"
                                       maxlength="15">
                                @error('telepon_tempat_praktik')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @else
                                    <div class="form-text">Nomor telepon yang bisa dihubungi (8-15 digit)</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus"></i> {{ __('Register') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-link">
                                    {{ __('Already have an account? Login') }}
                                </a>
                            </div>
                        </div>

                        {{-- Required Fields Note --}}
                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-4">
                                <small class="text-muted">
                                    <span class="text-danger">*</span> Menunjukkan field yang wajib diisi
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript untuk toggle password dan validasi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Real-time validation untuk NIK
    const nikInput = document.getElementById('nik');
    nikInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 16) {
            this.value = this.value.slice(0, 16);
        }
    });
    
    // Real-time validation untuk nomor telepon
    const telefonInput = document.getElementById('telepon_tempat_praktik');
    telefonInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '');
    });
    
    // Konfirmasi password match
    const passwordConfirm = document.getElementById('password-confirm');
    passwordConfirm.addEventListener('input', function() {
        if (this.value !== password.value) {
            this.setCustomValidity('Password tidak cocok');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validasi tanggal tidak boleh masa lalu
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                this.setCustomValidity('Tanggal tidak boleh masa lalu');
            } else {
                this.setCustomValidity('');
            }
        });
    });
});
</script>

<style>
.text-danger {
    color: #dc3545 !important;
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.alert {
    border-radius: 0.5rem;
}

.alert ul {
    padding-left: 1.5rem;
}

.form-text {
    color: #6c757d;
    font-size: 0.875em;
}

.input-group .btn {
    border-color: #ced4da;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.required-note {
    font-size: 0.875em;
    color: #6c757d;
}
</style>
@endsection