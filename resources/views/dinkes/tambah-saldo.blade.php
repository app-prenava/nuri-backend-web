@extends('layouts.app')
@section('title', 'Tambah Saldo Ibu Hamil')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Saldo Ibu Hamil</h1>
        <a href="{{ route('dinkes.dinkessaldo', $ibuHamil->id) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Saldo</h6>
                </div>
                <div class="card-body">
                    <!-- Display Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Detail Ibu Hamil -->
                    <div class="row mb-4">
                        <!-- Informasi Pribadi -->
                        <div class="col-md-6">
                            <div class="card border-left-primary">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-user-circle mr-2"></i>Informasi Pribadi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="40%">Nama Lengkap</th>
                                            <td>: {{ $ibuHamil->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Usia</th>
                                            <td>: {{ $ibuHamil->usia ?? '-' }} tahun</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Lahir</th>
                                            <td>: 
                                                @if($ibuHamil->tanggal_lahir)
                                                    {{ \Carbon\Carbon::parse($ibuHamil->tanggal_lahir)->format('d F Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>No. Telepon</th>
                                            <td>: {{ $ibuHamil->nomor_telepon ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>: {{ $ibuHamil->alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan</th>
                                            <td>: {{ $ibuHamil->pendidikan_terakhir ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pekerjaan</th>
                                            <td>: {{ $ibuHamil->pekerjaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Golongan Darah</th>
                                            <td>: 
                                                @if($ibuHamil->golongan_darah)
                                                    <span class="badge badge-info">{{ $ibuHamil->golongan_darah }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Suami & Kehamilan -->
                        <div class="col-md-6">
                            <!-- Informasi Suami -->
                            <div class="card border-left-info mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold text-info">
                                        <i class="fas fa-male mr-2"></i>Informasi Suami
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="40%">Nama Suami</th>
                                            <td>: {{ $ibuHamil->nama_suami ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Usia Suami</th>
                                            <td>: {{ $ibuHamil->usia_suami ?? '-' }} tahun</td>
                                        </tr>
                                        <tr>
                                            <th>Telepon Suami</th>
                                            <td>: {{ $ibuHamil->telepon_suami ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pekerjaan Suami</th>
                                            <td>: {{ $ibuHamil->pekerjaan_suami ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Informasi Kehamilan -->
                            <div class="card border-left-success mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold text-success">
                                        <i class="fas fa-baby mr-2"></i>Informasi Kehamilan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="50%">Usia Kehamilan</th>
                                            <td>: 
                                                @if($ibuHamil->usia_kehamilan)
                                                    <span class="badge badge-success">{{ $ibuHamil->usia_kehamilan }} minggu</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Trimester</th>
                                            <td>: 
                                                @if($ibuHamil->usia_kehamilan)
                                                    @php
                                                        $trimester = $ibuHamil->usia_kehamilan <= 12 ? '1' : 
                                                                   ($ibuHamil->usia_kehamilan <= 28 ? '2' : '3');
                                                    @endphp
                                                    <span class="badge badge-info">Trimester {{ $trimester }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @if($ibuHamil->usia_kehamilan)
                                        <tr>
                                            <th>Perkiraan Lahir</th>
                                            <td>: 
                                                @php
                                                    $perkiraanLahir = now()->addWeeks(40 - $ibuHamil->usia_kehamilan);
                                                @endphp
                                                {{ $perkiraanLahir->format('d M Y') }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Saldo Saat Ini -->
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-wallet fa-2x"></i>
                                    </div>
                                    <div class="text-center">Saldo Saat Ini</div>
                                    <div class="text-center h3 mb-0">Rp {{ number_format($ibuHamil->saldo_total ?? 0, 0, ',', '.') }}</div>
                                    <small class="text-white-50">
                                        Status: 
                                        <span class="badge {{ ($ibuHamil->saldo_total ?? 0) > 0 ? 'badge-success' : 'badge-danger' }}">
                                            {{ ($ibuHamil->saldo_total ?? 0) > 0 ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Tambah Saldo -->
                    <div class="card border-left-warning">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-warning">
                                <i class="fas fa-plus-circle mr-2"></i>Tambah Saldo Baru
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dinkes.tambah.saldo', $ibuHamil->id) }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="jumlah" class="col-sm-2 col-form-label">Jumlah Saldo (Rp)</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                               id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="1"
                                               placeholder="Masukkan jumlah saldo yang akan ditambahkan">
                                        @error('jumlah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                  id="keterangan" name="keterangan" rows="3" 
                                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Tambah Saldo
                                        </button>
                                        <a href="{{ route('dinkes.dinkessaldo', $ibuHamil->id) }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.table-sm th, .table-sm td {
    padding: 0.3rem;
    font-size: 0.875rem;
}
.card {
    border-radius: 0.35rem;
}
.badge {
    font-size: 0.75rem;
}
</style>
@endsection