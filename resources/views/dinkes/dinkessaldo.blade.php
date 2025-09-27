@extends('layouts.app')
@section('title', ' Ibu Hamil')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengelolaan Data Ibu Hamil</h1>
    </div>
    
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

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detail Ibu Hamil</h6>
                <div class="dropdown no-arrow">
                    <a href="{{ route('dinkes.laporan.saldo', $ibuHamil->id) }}" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-file-alt fa-sm text-white-50"></i> Laporan Transaksi
                    </a>
                </div>
            </div>
            <div class="card-body">
               
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Ibu</th>
                                <th>Usia</th>
                                <th>Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Nomor Telepon</th>
                                <th>Pendidikan Terakhir</th>
                                <th>Pekerjaan</th>
                                <th>Golongan Darah</th>
                                <th>Nama Suami</th>
                                <th>Telepon Suami</th>
                                <th>Usia Suami</th>
                                <th>Pekerjaan Suami</th>
                                <th>Saldo Total</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($ibuHamil)
                            <tr>
                                <td>1</td>
                                <td>{{ $ibuHamil->name }}</td>
                                <td>
                                    @php
                                        $birthDate = new DateTime($ibuHamil->tanggal_lahir);
                                        $today = new DateTime('today');
                                        $age = $birthDate->diff($today)->y;
                                    @endphp
                                    {{ $age }} tahun
                                </td>
                                <td>{{ $ibuHamil->tanggal_lahir }}</td>
                                <td>{{ $ibuHamil->alamat }}</td>
                                <td>{{ $ibuHamil->nomor_telepon }}</td>
                                <td>{{ $ibuHamil->pendidikan_terakhir }}</td>
                                <td>{{ $ibuHamil->pekerjaan }}</td>
                                <td>{{ $ibuHamil->golongan_darah }}</td>
                                <td>{{ $ibuHamil->nama_suami }}</td>
                                <td>{{ $ibuHamil->telepon_suami }}</td>
                                <td>{{ $ibuHamil->usia_suami }}</td>
                                <td>{{ $ibuHamil->pekerjaan_suami }}</td>
                                <td>Rp {{ number_format($ibuHamil->saldo_total, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('dinkes.form.tambah.saldo', $ibuHamil->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus-circle"></i> Tambah
                                    </a>
                                    <a href="{{ route('dinkes.riwayat.saldo', $ibuHamil->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-history"></i> Riwayat
                                    </a>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="15" class="text-center">Tidak ada data</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                
            </div>
        </div>
    </div>
</div>
</div>
@endsection