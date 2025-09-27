@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Hasil Deteksi Penyakit</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Pasien: {{ $deteksi->nama }}</h4>
                                <p class="text-muted">{{ $deteksi->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header {{ $deteksi->diabetes_prediction == 1 ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                    <h5 class="mb-0">Hasil Deteksi Diabetes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h2>
                                            @if($deteksi->diabetes_prediction == 1)
                                                <i class="fas fa-exclamation-triangle text-danger"></i> Positif
                                            @else
                                                <i class="fas fa-check-circle text-success"></i> Negatif
                                            @endif
                                        </h2>
                                    </div>
                                    <div>
                                        <p>Faktor risiko yang dianalisis:</p>
                                        <ul>
                                            <li>Usia: {{ $deteksi->age }} tahun</li>
                                            <li>BMI: {{ $deteksi->bmi }}</li>
                                            <li>Gula Darah: {{ $deteksi->bs }} mg/dL</li>
                                            <li>Tekanan Darah: {{ $deteksi->blood_pressure }}</li>
                                            <li>Jumlah Kehamilan: {{ $deteksi->pregnancies }}</li>
                                            @if($deteksi->skin_thickness)
                                                <li>Ketebalan Kulit: {{ $deteksi->skin_thickness }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-3">
                                        @if($deteksi->diabetes_prediction == 1)
                                            <p class="alert alert-danger">
                                                Hasil menunjukkan indikasi diabetes. Harap konsultasikan dengan dokter untuk diagnosis lebih lanjut.
                                            </p>
                                        @else
                                            <p class="alert alert-success">
                                                Hasil menunjukkan tidak ada indikasi diabetes. Tetap jaga pola hidup sehat.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header {{ $deteksi->hypertension_prediction == 1 ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                    <h5 class="mb-0">Hasil Deteksi Hipertensi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h2>
                                            @if($deteksi->hypertension_prediction == 1)
                                                <i class="fas fa-exclamation-triangle text-danger"></i> Positif
                                            @else
                                                <i class="fas fa-check-circle text-success"></i> Negatif
                                            @endif
                                        </h2>
                                    </div>
                                    <div>
                                        <p>Faktor risiko yang dianalisis:</p>
                                        <ul>
                                            <li>Usia: {{ $deteksi->age }} tahun</li>
                                            <li>BMI: {{ $deteksi->bmi }}</li>
                                            <li>Tekanan Sistolik: {{ $deteksi->systolic_bp ?? 'Tidak diisi' }}</li>
                                            <li>Tekanan Diastolik: {{ $deteksi->diastolic_bp ?? 'Tidak diisi' }}</li>
                                            <li>Detak Jantung: {{ $deteksi->heart_rate ?? 'Tidak diisi' }}</li>
                                            @if($deteksi->sex !== null)
                                                <li>Jenis Kelamin: {{ $deteksi->sex == 1 ? 'Laki-laki' : 'Perempuan' }}</li>
                                            @endif
                                            @if($deteksi->current_smoker !== null)
                                                <li>Status Merokok: {{ $deteksi->current_smoker == 1 ? 'Ya' : 'Tidak' }}</li>
                                            @endif
                                            @if($deteksi->cigs_per_day)
                                                <li>Jumlah Rokok per Hari: {{ $deteksi->cigs_per_day }}</li>
                                            @endif
                                            @if($deteksi->bp_meds !== null)
                                                <li>Konsumsi Obat Tekanan Darah: {{ $deteksi->bp_meds == 1 ? 'Ya' : 'Tidak' }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-3">
                                        @if($deteksi->hypertension_prediction == 1)
                                            <p class="alert alert-danger">
                                                Hasil menunjukkan indikasi hipertensi. Harap konsultasikan dengan dokter untuk diagnosis lebih lanjut.
                                            </p>
                                        @else
                                            <p class="alert alert-success">
                                                Hasil menunjukkan tidak ada indikasi hipertensi. Tetap jaga pola hidup sehat.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header 
                                    @if($deteksi->maternal_health_prediction === 'low risk')
                                        bg-warning text-dark
                                    @elseif($deteksi->maternal_health_prediction === 'high risk')
                                        bg-danger text-white
                                    @else
                                        bg-success text-white
                                    @endif">
                                    <h5 class="mb-0">Hasil Kesehatan Maternal</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h2>
                                            @if($deteksi->maternal_health_prediction === 'medium risk')
                                                <i class="fas fa-exclamation-circle text-warning"></i> Risiko Rendah
                                            @elseif($deteksi->maternal_health_prediction === 'high risk')
                                                <i class="fas fa-exclamation-triangle text-danger"></i> Risiko Tinggi
                                            @else
                                                <i class="fas fa-check-circle text-success"></i> Normal
                                            @endif
                                        </h2>
                                    </div>
                                    <div>
                                        <p>Faktor risiko yang dianalisis:</p>
                                        <ul>
                                            <li>Usia: {{ $deteksi->age }} tahun</li>
                                            <li>Gula Darah: {{ $deteksi->bs }} mg/dL</li>
                                            <li>Tekanan Sistolik: {{ $deteksi->systolic_bp ?? 'Tidak diisi' }}</li>
                                            <li>Tekanan Diastolik: {{ $deteksi->diastolic_bp ?? 'Tidak diisi' }}</li>
                                            <li>Detak Jantung: {{ $deteksi->heart_rate ?? 'Tidak diisi' }}</li>
                                            <li>Suhu Tubuh: {{ $deteksi->body_temp ?? 'Tidak diisi' }} °C</li>
                                        </ul>
                                    </div>

                                    <div class="mt-3">
                                        @if($deteksi->maternal_health_prediction === 'medium risk')
                                            <p class="alert alert-warning">
                                                Hasil menunjukkan risiko rendah pada kesehatan maternal. Disarankan untuk berkonsultasi dengan dokter.
                                            </p>
                                        @elseif($deteksi->maternal_health_prediction === 'high risk')
                                            <p class="alert alert-danger">
                                                Hasil menunjukkan risiko tinggi pada kesehatan maternal. Harap segera konsultasi dengan dokter.
                                            </p>
                                        @else
                                            <p class="alert alert-success">
                                                Hasil menunjukkan kondisi kesehatan maternal normal. Tetap jaga pola hidup sehat.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Catatan:</strong> Hasil deteksi ini hanya bersifat prediktif dan tidak dapat dijadikan sebagai diagnosa medis. 
                                Silakan konsultasikan dengan dokter untuk pemeriksaan lebih lanjut.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('deteksi.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                                <a href="{{ route('deteksi.create') }}" class="btn btn-primary">Deteksi Baru</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection