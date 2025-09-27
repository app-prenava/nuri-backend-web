@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Detail Prediksi Depresi</h3>
        <a href="{{ route('depresi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- User Information Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Informasi Pengguna</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $prediksi->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $prediksi->user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Umur:</strong></td>
                            <td>
                                <span class="badge bg-info">{{ $prediksi->user->usia ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Prediksi:</strong></td>
                            <td>{{ $prediksi->created_at->format('d F Y, H:i') }} WIB</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    <!-- Prediction Result Card -->
    <div class="col-md-6 mb-4">
        <div class="card">
            @php
                $finalResult = false;
                $resultText = 'Tidak Bergejala Depresi';
                $resultDescription = 'Tidak menunjukkan indikasi depresi berdasarkan analisis gejala dan skor EPDS.';
                $badgeClass = 'bg-success';
                $headerClass = 'bg-success';
                
                // Logika berdasarkan hasil prediksi dan skor EPDS (konsisten dengan index)
                if ($prediksi->hasil_prediksi == 1) {
                    if ($prediksi->epds && $prediksi->epds->score >= 13) {
                        $finalResult = true;
                        $resultText = 'Resiko Tinggi Depresi';
                        $resultDescription = 'Menunjukkan gejala depresi dengan skor EPDS tinggi (skor: ' . $prediksi->epds->score . '). Memerlukan evaluasi medis segera.';
                        $badgeClass = 'bg-danger';
                        $headerClass = 'bg-danger';
                    } elseif ($prediksi->epds && $prediksi->epds->score >= 10) {
                        $finalResult = true;
                        $resultText = 'Kemungkinan Gejala Depresi';
                        $resultDescription = 'Menunjukkan gejala depresi dengan skor EPDS sedang (skor: ' . $prediksi->epds->score . '). Disarankan untuk konsultasi lebih lanjut.';
                        $badgeClass = 'bg-warning';
                        $headerClass = 'bg-warning';
                    } else {
                        $finalResult = false;
                        $resultText = 'Tidak Bergejala Depresi';
                        if ($prediksi->epds) {
                            $resultDescription = 'Meskipun ada gejala awal, skor EPDS rendah menunjukkan risiko depresi rendah (skor: ' . $prediksi->epds->score . ').';
                        } else {
                            $resultDescription = 'Gejala awal terdeteksi namun tidak ada data EPDS untuk konfirmasi lebih lanjut.';
                        }
                        $badgeClass = 'bg-success';
                        $headerClass = 'bg-success';
                    }
                } elseif ($prediksi->hasil_prediksi == 0) {
                    if ($prediksi->epds && $prediksi->epds->score >= 13) {
                        $finalResult = true;
                        $resultText = 'Resiko Tinggi Depresi';
                        $resultDescription = 'Meskipun gejala awal tidak terdeteksi, skor EPDS tinggi menunjukkan risiko depresi (skor: ' . $prediksi->epds->score . '). Memerlukan evaluasi medis segera.';
                        $badgeClass = 'bg-danger';
                        $headerClass = 'bg-danger';
                    } elseif ($prediksi->epds && $prediksi->epds->score >= 10) {
                        $finalResult = true;
                        $resultText = 'Kemungkinan Gejala Depresi';
                        $resultDescription = 'Meskipun gejala awal tidak terdeteksi, skor EPDS menunjukkan kemungkinan depresi (skor: ' . $prediksi->epds->score . '). Disarankan untuk konsultasi lebih lanjut.';
                        $badgeClass = 'bg-warning';
                        $headerClass = 'bg-warning';
                    } else {
                        $finalResult = false;
                        $resultText = 'Tidak Bergejala Depresi';
                        if ($prediksi->epds) {
                            $resultDescription = 'Tidak menunjukkan indikasi depresi berdasarkan gejala dan skor EPDS yang rendah (skor: ' . $prediksi->epds->score . ').';
                        } else {
                            $resultDescription = 'Tidak menunjukkan indikasi depresi berdasarkan gejala yang dilaporkan.';
                        }
                        $badgeClass = 'bg-success';
                        $headerClass = 'bg-success';
                    }
                }
            @endphp
            
            <div class="card-header {{ $headerClass }} text-white">
                <h5 class="mb-0"><i class="fas fa-diagnoses"></i> Hasil Prediksi</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge {{ $badgeClass }} fs-5 p-3">
                        {{ $resultText }}
                    </span>
                </div>
                <p class="text-muted">
                    {{ $resultDescription }}
                </p>
                
                <!-- Info tambahan untuk transparansi -->
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <strong>Detail Analisis:</strong><br>
                        Gejala Awal: {{ $prediksi->hasil_prediksi ? 'Positif' : 'Negatif' }}
                        @if($prediksi->epds)
                            | Skor EPDS: {{ $prediksi->epds->score }}/30
                        @else
                            | EPDS: Tidak tersedia
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Depression Symptoms Analysis -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-heartbeat"></i> Analisis Gejala Depresi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-sad-tear text-info"></i> Merasa Sedih</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->merasa_sedih == 0 ? 'bg-success' : ($prediksi->merasa_sedih == 1 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getMerasaSedihTextAttribute() }}
                            </span>
                        </p>
                    </div>

                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-angry text-danger"></i> Mudah Tersinggung</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->mudah_tersinggung == 0 ? 'bg-success' : ($prediksi->mudah_tersinggung == 1 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getMudahTersinggungTextAttribute() }}
                            </span>
                        </p>
                    </div>

                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-bed text-primary"></i> Masalah Tidur</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->masalah_tidur == 0 ? 'bg-success' : ($prediksi->masalah_tidur == 1 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getMasalahTidurTextAttribute() }}
                            </span>
                        </p>
                    </div>

                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-brain text-purple"></i> Masalah Fokus</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->masalah_fokus == 0 ? 'bg-success' : ($prediksi->masalah_fokus == 1 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getMasalahFokusTextAttribute() }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-utensils text-success"></i> Perubahan Pola Makan</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->pola_makan == 0 ? 'bg-success' : ($prediksi->pola_makan == 2 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getPolaMakanTextAttribute() }}
                            </span>
                        </p>
                    </div>

                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-frown text-secondary"></i> Merasa Bersalah</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->merasa_bersalah == 0 ? 'bg-success' : ($prediksi->merasa_bersalah == 1 ? 'bg-danger' : 'bg-warning') }}">
                                {{ $prediksi->getMerasaBersalahTextAttribute() }}
                            </span>
                        </p>
                    </div>

                    <div class="symptom-item mb-3">
                        <h6><i class="fas fa-exclamation-triangle text-danger"></i> Pikiran Bunuh Diri</h6>
                        <p class="ms-4">
                            <span class="badge {{ $prediksi->suicide_attempt == 0 ? 'bg-success' : ($prediksi->suicide_attempt == 1 ? 'bg-danger' : 'bg-secondary') }}">
                                {{ $prediksi->getSuicideAttemptTextAttribute() }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EPDS Score Card -->
    @if($prediksi->epds)
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Skor EPDS (Edinburgh Postnatal Depression Scale)</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h2 class="display-4 
                            @if($prediksi->epds->score >= 13) text-danger
                            @elseif($prediksi->epds->score >= 10) text-warning
                            @else text-success
                            @endif">
                            {{ $prediksi->epds->score }}
                        </h2>
                        <p class="text-muted">Total Skor</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <span class="badge 
                            @if($prediksi->epds->score >= 13) bg-danger
                            @elseif($prediksi->epds->score >= 10) bg-warning
                            @else bg-success
                            @endif fs-6 p-2">
                            @if($prediksi->epds->score >= 13)
                                Resiko Tinggi Depresi
                            @elseif($prediksi->epds->score >= 10)
                                Kemungkinan Gejala Depresi
                            @else
                                Gejala Ringan
                            @endif
                        </span>
                        <p class="text-muted mt-2">Hasil EPDS</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-info">Interpretasi:</h6>
                        <p class="small">
                            @if($prediksi->epds->score >= 13)
                                Skor ≥ 13 menunjukkan risiko tinggi depresi dan memerlukan evaluasi medis segera.
                            @elseif($prediksi->epds->score >= 10)
                                Skor 10-13 menunjukkan kemungkinan depresi dan disarankan untuk konsultasi lebih lanjut.
                            @else
                                Skor < 10 menunjukkan gejala ringan dengan risiko depresi rendah.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($prediksi->epds->answers)
            <hr>
            <h6><i class="fas fa-list"></i> Detail Jawaban EPDS:</h6>
            <div class="row">
                @php
                    $epdsQuestions = [
                        'Dapat tertawa dan melihat sisi lucu dari berbagai hal',
                        'Dapat menantikan sesuatu dengan perasaan senang',
                        'Menyalahkan diri sendiri tanpa alasan ketika hal buruk terjadi',
                        'Merasa cemas atau khawatir tanpa alasan yang jelas',
                        'Merasa takut atau panik tanpa alasan yang jelas',
                        'Merasa kewalahan dengan berbagai hal',
                        'Sulit tidur karena merasa tidak bahagia',
                        'Merasa sedih atau sengsara',
                        'Merasa tidak bahagia sampai menangis',
                        'Terlintas pikiran untuk menyakiti diri sendiri'
                    ];
                @endphp
                @foreach($prediksi->epds->answers as $index => $answer)
                <div class="col-md-6 mb-2">
                    <small class="text-muted">{{ $epdsQuestions[$index] ?? 'Pertanyaan ' . ($index + 1) }}:</small>
                    <span class="badge bg-light text-dark">{{ $answer }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Skor EPDS</h5>
        </div>
        <div class="card-body text-center">
            <p class="text-muted">Data EPDS tidak tersedia untuk prediksi ini.</p>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-4 text-center">
        <a href="{{ route('depresi.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <form action="{{ route('depresi.destroy', $prediksi->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus data prediksi ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Data
            </button>
        </form>
    </div>
</div>

<style>
.text-purple {
    color: #6f42c1 !important;
}
.symptom-item h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.display-4 {
    font-weight: bold;
}
</style>
@endsection