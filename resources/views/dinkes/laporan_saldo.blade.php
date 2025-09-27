
@extends('layouts.app')

@section('title', 'Laporan Saldo Ibu Hamil')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Saldo - {{ $user->name }}</h5>
                    <a href="{{ route('dinkes.dashboardd') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Ringkasan Saldo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title text-success">Total Masuk</h6>
                                                    <h5>Rp {{ number_format($totalCredit, 0, ',', '.') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title text-danger">Total Keluar</h6>
                                                    <h5>Rp {{ number_format($totalDebit, 0, ',', '.') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mt-3">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Saldo Saat Ini</h6>
                                            <h4 class="{{ ($totalCredit - $totalDebit) >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($totalCredit - $totalDebit, 0, ',', '.') }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabel Riwayat Transaksi -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Riwayat Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="table-transaksi">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transaksi as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                                <td>
                                                    @if($item->type == 'credit')
                                                        <span class="badge bg-success">Masuk</span>
                                                    @else
                                                        <span class="badge bg-danger">Keluar</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="{{ $item->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->keterangan ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada transaksi</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Cetak -->
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#table-transaksi').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush