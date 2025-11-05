@extends('layouts.app')

@section('title', 'WMS Dashboard')
@section('page-title', 'WMS Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Warehouse Management System Dashboard</h5>
            </div>
            <div class="card-body">
                <p>Selamat datang di sistem manajemen gudang. Pilih menu di sebelah kiri untuk mulai mengelola data gudang Anda.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6><i class="fas fa-building me-2"></i>Manajemen Gudang</h6>
                                <p class="small">Kelola data master gudang</p>
                                <a href="{{ route('gudang.index') }}" class="btn btn-light btn-sm">Buka</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6><i class="fas fa-boxes me-2"></i>Produk Business</h6>
                                <p class="small">Kelola data produk business</p>
                                <a href="{{ route('product-business.index') }}" class="btn btn-light btn-sm">Buka</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6><i class="fas fa-chart-line me-2"></i>Laporan</h6>
                                <p class="small">Lihat laporan dan analisis</p>
                                <a href="#" class="btn btn-light btn-sm">Buka</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection