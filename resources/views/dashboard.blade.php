@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard WMS')

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Gudang
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-gudang">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Produk
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-produk">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Stock Movement Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Low Stock Alert
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Stock Movement Overview</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Gudang Status</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="gudangStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="recent-activities">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: .25rem solid #4e73df!important;
    }
    .border-left-success {
        border-left: .25rem solid #1cc88a!important;
    }
    .border-left-info {
        border-left: .25rem solid #36b9cc!important;
    }
    .border-left-warning {
        border-left: .25rem solid #f6c23e!important;
    }
    .text-xs {
        font-size: .75rem;
    }
    .font-weight-bold {
        font-weight: 700!important;
    }
    .text-gray-800 {
        color: #5a5c69!important;
    }
    .text-gray-300 {
        color: #dddfeb!important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Load dashboard data
    loadDashboardData();
    
    // Initialize charts
    initCharts();
});

function loadDashboardData() {
    // Load total gudang
    fetch('/api/wms/gudang')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-gudang').textContent = data.data.length;
            }
        })
        .catch(error => console.error('Error loading gudang data:', error));
    
    // Load total produk
    fetch('/api/wms/product-business')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-produk').textContent = data.data.length;
            }
        })
        .catch(error => console.error('Error loading product data:', error));
    
    // Load inventory analytics
    fetch('/api/wms/inventory/analytics/dashboard')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stock movement card
                const stockMovementCard = document.querySelector('.col-xl-3:nth-child(3) .h5');
                if (stockMovementCard) {
                    stockMovementCard.textContent = data.data.today_transactions;
                }
                
                // Update low stock alert card
                const lowStockCard = document.querySelector('.col-xl-3:nth-child(4) .h5');
                if (lowStockCard) {
                    lowStockCard.textContent = data.data.low_stock_items;
                }
            }
        })
        .catch(error => console.error('Error loading inventory analytics:', error));
}

function initCharts() {
    // Stock Movement Chart
    const ctx1 = document.getElementById('stockMovementChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Stock In',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.3
                }, {
                    label: 'Stock Out',
                    data: [2, 3, 20, 5, 1, 4],
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
    
    // Gudang Status Chart
    const ctx2 = document.getElementById('gudangStatusChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive', 'Maintenance'],
                datasets: [{
                    data: [75, 20, 5],
                    backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
}
</script>
@endpush