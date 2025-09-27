@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Form Deteksi Penyakit</h3>
                    <small class="text-light">Mohon isi form dengan data yang akurat untuk hasil deteksi yang optimal</small>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('deteksi.store') }}">
                        @csrf
                        
                        <!-- Data Pribadi -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Data Pribadi
                                </h4>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nama Lengkap 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama') }}" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Gunakan nama lengkap sesuai identitas
                                </div>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="age" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Umur 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('age') is-invalid @enderror" 
                                           id="age" 
                                           name="age" 
                                           value="{{ old('age') }}" 
                                           placeholder="25"
                                           min="15" 
                                           max="100"
                                           required>
                                    <span class="input-group-text">tahun</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan umur dalam tahun (15-100 tahun)
                                </div>
                                @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Data Fisik -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-weight me-2"></i>Data Fisik
                                </h4>
                            </div>
                            
                            <!-- Input Tinggi Badan -->
                            <div class="col-md-4 mb-3">
                                <label for="tinggi_badan" class="form-label">
                                    <i class="fas fa-ruler-vertical me-1"></i>Tinggi Badan 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control @error('tinggi_badan') is-invalid @enderror" 
                                           id="tinggi_badan" 
                                           name="tinggi_badan" 
                                           value="{{ old('tinggi_badan') }}" 
                                           placeholder="160"
                                           min="100" 
                                           max="250"
                                           required>
                                    <span class="input-group-text">cm</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan tinggi badan dalam centimeter
                                </div>
                                @error('tinggi_badan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Berat Badan -->
                            <div class="col-md-4 mb-3">
                                <label for="berat_badan" class="form-label">
                                    <i class="fas fa-weight me-1"></i>Berat Badan 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control @error('berat_badan') is-invalid @enderror" 
                                           id="berat_badan" 
                                           name="berat_badan" 
                                           value="{{ old('berat_badan') }}" 
                                           placeholder="55"
                                           min="30" 
                                           max="200"
                                           required>
                                    <span class="input-group-text">kg</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan berat badan dalam kilogram
                                </div>
                                @error('berat_badan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- BMI Display (Calculated automatically) -->
                            <div class="col-md-4 mb-3">
                                <label for="bmi_display" class="form-label">
                                    <i class="fas fa-calculator me-1"></i>BMI (Body Mass Index)
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="bmi_display" 
                                           readonly 
                                           placeholder="Akan dihitung otomatis">
                                    <span class="input-group-text">kg/m²</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Normal:</strong> 18.5-24.9 | <strong>Berlebih:</strong> 25-29.9 | <strong>Obesitas:</strong> ≥30
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="systolic_bp" class="form-label">
                                    <i class="fas fa-heartbeat me-1"></i>Tekanan Sistolik 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('systolic_bp') is-invalid @enderror" 
                                           id="systolic_bp" 
                                           name="systolic_bp" 
                                           value="{{ old('systolic_bp', 120) }}" 
                                           placeholder="120"
                                           min="80" 
                                           max="250"
                                           required>
                                    <span class="input-group-text">mmHg</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Angka atas saat mengukur tekanan darah
                                </div>
                                @error('systolic_bp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="diastolic_bp" class="form-label">
                                    <i class="fas fa-heartbeat me-1"></i>Tekanan Diastolik 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('diastolic_bp') is-invalid @enderror" 
                                           id="diastolic_bp" 
                                           name="diastolic_bp" 
                                           value="{{ old('diastolic_bp', 80) }}" 
                                           placeholder="80"
                                           min="50" 
                                           max="150"
                                           required>
                                    <span class="input-group-text">mmHg</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Angka bawah saat mengukur tekanan darah
                                </div>
                                @error('diastolic_bp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- MAP Display -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-chart-line me-1"></i>Mean Arterial Pressure (MAP)
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="map_display" 
                                           readonly 
                                           placeholder="Akan dihitung otomatis">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Rumus:</strong> MAP = (Sistolik + 2 × Diastolik) ÷ 3
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="skin_thickness" class="form-label">
                                    <i class="fas fa-ruler me-1"></i>Ketebalan Kulit
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control @error('skin_thickness') is-invalid @enderror" 
                                           id="skin_thickness" 
                                           name="skin_thickness" 
                                           value="{{ old('skin_thickness') }}" 
                                           placeholder="20"
                                           min="0" 
                                           max="100">
                                    <span class="input-group-text">mm</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Ketebalan lipatan kulit (opsional, kosongkan jika tidak diketahui)
                                </div>
                                @error('skin_thickness')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Data Kesehatan -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-stethoscope me-2"></i>Data Kesehatan
                                </h4>
                            </div>
                            
                            <!-- Input Gula Darah dengan Konversi Otomatis -->
                            <div class="col-md-6 mb-3">
                                <label for="bs_mgdl" class="form-label">
                                    <i class="fas fa-tint me-1"></i>Gula Darah 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control @error('bs_mgdl') is-invalid @enderror" 
                                           id="bs_mgdl" 
                                           name="bs_mgdl" 
                                           value="{{ old('bs_mgdl') }}" 
                                           placeholder="100"
                                           min="50" 
                                           max="500"
                                           required>
                                    <span class="input-group-text">mg/dL</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Normal:</strong> 70-100 (puasa) | 70-140 (setelah makan)<br>
                                    <small>Diabetes: ≥126 (puasa) atau ≥200 (sewaktu)</small>
                                </div>
                                @error('bs_mgdl')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Display Konversi ke mmol/L -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-exchange-alt me-1"></i>Gula Darah (mmol/L)
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="bs_mmol_display" 
                                           readonly 
                                           placeholder="Akan dikonversi otomatis">
                                    <span class="input-group-text">mmol/L</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Konversi:</strong> mg/dL ÷ 18.0182 = mmol/L<br>
                                    <small><strong>Normal mmol/L:</strong> 3.9-5.6 (puasa) | 3.9-7.8 (setelah makan)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="pregnancies" class="form-label">
                                    <i class="fas fa-baby me-1"></i>Jumlah Kehamilan 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('pregnancies') is-invalid @enderror" 
                                           id="pregnancies" 
                                           name="pregnancies" 
                                           value="{{ old('pregnancies', 0) }}" 
                                           placeholder="0"
                                           min="0" 
                                           max="20"
                                           required>
                                    <span class="input-group-text">kali</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Jumlah total kehamilan (termasuk yang sedang berlangsung)
                                </div>
                                @error('pregnancies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="heart_rate" class="form-label">
                                    <i class="fas fa-heart me-1"></i>Detak Jantung
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('heart_rate') is-invalid @enderror" 
                                           id="heart_rate" 
                                           name="heart_rate" 
                                           value="{{ old('heart_rate', 70) }}" 
                                           placeholder="70"
                                           min="40" 
                                           max="200">
                                    <span class="input-group-text">bpm</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Normal:</strong> 60-100 bpm saat istirahat
                                </div>
                                @error('heart_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="body_temp" class="form-label">
                                    <i class="fas fa-thermometer-half me-1"></i>Suhu Tubuh
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.1" 
                                           class="form-control @error('body_temp') is-invalid @enderror" 
                                           id="body_temp" 
                                           name="body_temp" 
                                           value="{{ old('body_temp', 36.6) }}" 
                                           placeholder="36.6"
                                           min="35" 
                                           max="42">
                                    <span class="input-group-text">°C</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Normal:</strong> 36.1-37.2°C
                                </div>
                                @error('body_temp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="bp_meds" class="form-label">
                                    <i class="fas fa-pills me-1"></i>Konsumsi Obat Tekanan Darah
                                </label>
                                <select class="form-select @error('bp_meds') is-invalid @enderror" id="bp_meds" name="bp_meds">
                                    <option value="">Pilih Opsi</option>
                                    <option value="1" {{ old('bp_meds') == '1' ? 'selected' : '' }}>Ya, sedang mengonsumsi</option>
                                    <option value="0" {{ old('bp_meds') == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Apakah saat ini mengonsumsi obat untuk tekanan darah tinggi?
                                </div>
                                @error('bp_meds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Informasi Gaya Hidup -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-running me-2"></i>Informasi Gaya Hidup
                                </h4>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="current_smoker" class="form-label">
                                    <i class="fas fa-smoking me-1"></i>Status Merokok
                                </label>
                                <select class="form-select @error('current_smoker') is-invalid @enderror" id="current_smoker" name="current_smoker">
                                    <option value="">Pilih Status</option>
                                    <option value="1" {{ old('current_smoker') == '1' ? 'selected' : '' }}>Ya, saya merokok</option>
                                    <option value="0" {{ old('current_smoker') == '0' ? 'selected' : '' }}>Tidak merokok</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Status merokok saat ini (dalam 30 hari terakhir)
                                </div>
                                @error('current_smoker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3" id="cigs_container" style="display: none;">
                                <label for="cigs_per_day" class="form-label">
                                    <i class="fas fa-calculator me-1"></i>Jumlah Rokok per Hari
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('cigs_per_day') is-invalid @enderror" 
                                           id="cigs_per_day" 
                                           name="cigs_per_day" 
                                           value="{{ old('cigs_per_day', 0) }}" 
                                           placeholder="10"
                                           min="0" 
                                           max="100">
                                    <span class="input-group-text">batang</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Rata-rata jumlah rokok yang dikonsumsi per hari
                                </div>
                                @error('cigs_per_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="sex" value="0">
                        
                        <!-- Submit Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title text-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                                        </h5>
                                        <p class="card-text mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Hasil deteksi ini hanya untuk referensi awal dan tidak menggantikan konsultasi medis profesional. 
                                            Pastikan semua data yang dimasukkan akurat untuk hasil yang optimal.
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('deteksi.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Mulai Deteksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const smokerSelect = document.getElementById('current_smoker');
    const cigsContainer = document.getElementById('cigs_container');
    const cigsPerDayField = document.getElementById('cigs_per_day');
    const systolicInput = document.getElementById('systolic_bp');
    const diastolicInput = document.getElementById('diastolic_bp');
    const mapDisplay = document.getElementById('map_display');
    
    // Input untuk BMI calculation
    const tinggiInput = document.getElementById('tinggi_badan');
    const beratInput = document.getElementById('berat_badan');
    const bmiDisplay = document.getElementById('bmi_display');
    
    // Input untuk Blood Sugar conversion
    const bsMgdlInput = document.getElementById('bs_mgdl');
    const bsMmolDisplay = document.getElementById('bs_mmol_display');
    
    // Toggle cigarettes per day field
    function toggleCigsPerDay() {
        if (smokerSelect.value === '1') {
            cigsContainer.style.display = 'block';
            cigsContainer.classList.add('animate__animated', 'animate__fadeIn');
        } else {
            cigsContainer.style.display = 'none';
            cigsPerDayField.value = '0';
        }
    }
    
    // Calculate BMI automatically
    function calculateBMI() {
        const tinggi = parseFloat(tinggiInput.value) || 0;
        const berat = parseFloat(beratInput.value) || 0;
        
        if (tinggi > 0 && berat > 0) {
            const tinggiMeter = tinggi / 100; // Convert cm to meters
            const bmi = (berat / (tinggiMeter * tinggiMeter)).toFixed(2);
            bmiDisplay.value = bmi;
            
            // Add color coding for BMI values
            bmiDisplay.classList.remove('text-success', 'text-warning', 'text-danger');
            if (bmi < 18.5) {
                bmiDisplay.classList.add('text-warning');
            } else if (bmi >= 18.5 && bmi <= 24.9) {
                bmiDisplay.classList.add('text-success');
            } else if (bmi >= 25 && bmi <= 29.9) {
                bmiDisplay.classList.add('text-warning');
            } else {
                bmiDisplay.classList.add('text-danger');
            }
        } else {
            bmiDisplay.value = '';
        }
    }
    
    // Convert Blood Sugar from mg/dL to mmol/L
    function convertBloodSugar() {
        const bsMgdl = parseFloat(bsMgdlInput.value) || 0;
        
        if (bsMgdl > 0) {
            const bsMmol = (bsMgdl / 18.0182).toFixed(2);
            bsMmolDisplay.value = bsMmol;
            
            // Add color coding for blood sugar values (mg/dL)
            bsMgdlInput.classList.remove('border-success', 'border-warning', 'border-danger');
            bsMmolDisplay.classList.remove('text-success', 'text-warning', 'text-danger');
            
            if (bsMgdl < 70) {
                bsMgdlInput.classList.add('border-warning');
                bsMmolDisplay.classList.add('text-warning');
            } else if (bsMgdl >= 70 && bsMgdl <= 100) {
                bsMgdlInput.classList.add('border-success');
                bsMmolDisplay.classList.add('text-success');
            } else if (bsMgdl > 100 && bsMgdl < 126) {
                bsMgdlInput.classList.add('border-warning');
                bsMmolDisplay.classList.add('text-warning');
            } else {
                bsMgdlInput.classList.add('border-danger');
                bsMmolDisplay.classList.add('text-danger');
            }
        } else {
            bsMmolDisplay.value = '';
        }
    }
    
    // Calculate MAP (Mean Arterial Pressure)
    function calculateMAP() {
        const systolic = parseFloat(systolicInput.value) || 0;
        const diastolic = parseFloat(diastolicInput.value) || 0;
        
        if (systolic > 0 && diastolic > 0) {
            const map = ((systolic + (2 * diastolic)) / 3).toFixed(1);
            mapDisplay.value = map;
            
            // Add color coding for MAP values
            mapDisplay.classList.remove('text-success', 'text-warning', 'text-danger');
            if (map < 70) {
                mapDisplay.classList.add('text-danger');
            } else if (map > 100) {
                mapDisplay.classList.add('text-warning');
            } else {
                mapDisplay.classList.add('text-success');
            }
        } else {
            mapDisplay.value = '';
        }
    }
    
    // Event listeners
    smokerSelect.addEventListener('change', toggleCigsPerDay);
    systolicInput.addEventListener('input', calculateMAP);
    diastolicInput.addEventListener('input', calculateMAP);
    tinggiInput.addEventListener('input', calculateBMI);
    beratInput.addEventListener('input', calculateBMI);
    bsMgdlInput.addEventListener('input', convertBloodSugar);
    
    // Initial calculations
    toggleCigsPerDay();
    calculateMAP();
    calculateBMI();
    convertBloodSugar();
    
    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = document.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Validate BMI calculation
        const bmi = parseFloat(bmiDisplay.value);
        if (!bmi || bmi < 10 || bmi > 50) {
            alert('BMI tidak valid. Pastikan tinggi dan berat badan diisi dengan benar.');
            tinggiInput.focus();
            e.preventDefault();
            return;
        }
        
        // Validate blood sugar conversion
        const bsMmol = parseFloat(bsMmolDisplay.value);
        if (!bsMmol || bsMmol < 2.8 || bsMmol > 27.8) {
            alert('Nilai gula darah tidak valid. Pastikan nilai gula darah dalam range yang wajar.');
            bsMgdlInput.focus();
            e.preventDefault();
            return;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (bertanda *)');
            
            // Scroll to first invalid field
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });
    
    // Add tooltips for better UX
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<style>
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.border-success {
    border-color: #198754 !important;
}

.border-warning {
    border-color: #ffc107 !important;
}

.border-danger {
    border-color: #dc3545 !important;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.form-control, .form-select {
    border-radius: 8px;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.animate__fadeIn {
    animation-duration: 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate__animated.animate__fadeIn {
    animation-name: fadeIn;
}

/* Styling untuk readonly inputs dengan hasil perhitungan */
.bg-light {
    background-color: #f8f9fa !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}
</style>
@endsection