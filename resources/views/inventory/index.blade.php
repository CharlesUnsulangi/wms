@extends('layouts.app')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')

@section('content')
<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Transaksi Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="today-transactions">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
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
                            Stock In Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="today-stock-in">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                            Stock Out Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="today-stock-out">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
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
                            Low Stock Items
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="low-stock-items">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="stock-summary-tab" data-bs-toggle="tab" data-bs-target="#stock-summary" type="button" role="tab">
            <i class="fas fa-boxes me-2"></i>Stock Summary
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
            <i class="fas fa-list me-2"></i>Transactions
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="movement-history-tab" data-bs-toggle="tab" data-bs-target="#movement-history" type="button" role="tab">
            <i class="fas fa-history me-2"></i>Movement History
        </button>
    </li>
</ul>

<div class="tab-content" id="inventoryTabsContent">
    <!-- Stock Summary Tab -->
    <div class="tab-pane fade show active" id="stock-summary" role="tabpanel">
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Current Stock Summary</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshStockSummary()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="stockSummaryTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Warehouse</th>
                                <th>SKU Business</th>
                                <th>Current Stock</th>
                                <th>Last Price</th>
                                <th>Stock Value</th>
                                <th>Last Transaction</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Tab -->
    <div class="tab-pane fade" id="transactions" role="tabpanel">
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Inventory Transactions</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="warehouseFilter">
                            <option value="">All Warehouses</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="transactionTypeFilter">
                            <option value="">All Transaction Types</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="dateFromFilter" placeholder="Date From">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="dateToFilter" placeholder="Date To">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="transactionsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Transaction Code</th>
                                <th>Warehouse</th>
                                <th>SKU Business</th>
                                <th>Type</th>
                                <th>Qty In</th>
                                <th>Qty Out</th>
                                <th>Final Stock</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement History Tab -->
    <div class="tab-pane fade" id="movement-history" role="tabpanel">
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Stock Movement History</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select class="form-select" id="movementWarehouseFilter" required>
                            <option value="">Select Warehouse</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="movementSkuFilter" placeholder="Enter SKU Business" required>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" onclick="loadMovementHistory()">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="movementHistoryTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Transaction</th>
                                <th>Type</th>
                                <th>Qty In</th>
                                <th>Qty Out</th>
                                <th>Balance</th>
                                <th>Price</th>
                                <th>Tracking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Select warehouse and SKU to view movement history
                                </td>
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
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load dashboard analytics
    loadDashboardAnalytics();
    
    // Initialize tables
    initializeStockSummaryTable();
    initializeTransactionsTable();
    
    // Load filter options
    loadFilterOptions();
});

function loadDashboardAnalytics() {
    fetch('/api/wms/inventory/analytics/dashboard')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('today-transactions').textContent = data.data.today_transactions;
                document.getElementById('today-stock-in').textContent = parseFloat(data.data.today_stock_in).toLocaleString('id-ID');
                document.getElementById('today-stock-out').textContent = parseFloat(data.data.today_stock_out).toLocaleString('id-ID');
                document.getElementById('low-stock-items').textContent = data.data.low_stock_items;
            }
        })
        .catch(error => {
            console.error('Error loading analytics:', error);
        });
}

function initializeStockSummaryTable() {
    $('#stockSummaryTable').DataTable({
        ajax: {
            url: '/api/wms/inventory/stock/summary',
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'warehouse_code' },
            { data: 'sku_business' },
            { 
                data: 'current_stock',
                render: function(data) {
                    return parseFloat(data).toLocaleString('id-ID');
                }
            },
            { 
                data: 'last_price',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const stock = parseFloat(row.current_stock) || 0;
                    const price = parseFloat(row.last_price) || 0;
                    const value = stock * price;
                    return 'Rp ' + value.toLocaleString('id-ID');
                }
            },
            { 
                data: 'last_transaction_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { 
                data: 'current_stock',
                render: function(data) {
                    const stock = parseFloat(data);
                    if (stock <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    } else if (stock < 10) {
                        return '<span class="badge bg-warning">Low Stock</span>';
                    } else {
                        return '<span class="badge bg-success">In Stock</span>';
                    }
                }
            }
        ],
        responsive: true,
        pageLength: 25,
        order: [[2, 'asc']] // Sort by current stock
    });
}

function initializeTransactionsTable() {
    $('#transactionsTable').DataTable({
        ajax: {
            url: '/api/wms/inventory',
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { 
                data: 'rec_datecreated',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { data: 'tr_main_code' },
            { data: 'main_ms_inv' },
            { 
                data: 'main_ms_sku_business',
                render: function(data) {
                    return data.length > 20 ? data.substring(0, 20) + '...' : data;
                }
            },
            { data: 'main_type_transaksi' },
            { 
                data: 'main_qty_in',
                render: function(data) {
                    return data ? parseFloat(data).toLocaleString('id-ID') : '0';
                }
            },
            { 
                data: 'main_qtu_out',
                render: function(data) {
                    return data ? parseFloat(data).toLocaleString('id-ID') : '0';
                }
            },
            { 
                data: 'main_stock_akhir',
                render: function(data) {
                    return data ? parseFloat(data).toLocaleString('id-ID') : '0';
                }
            },
            { 
                data: 'main_inv_price',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                }
            }
        ],
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']] // Sort by date descending
    });
}

function loadFilterOptions() {
    // Load warehouses for filters
    fetch('/api/wms/gudang')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const warehouseSelects = ['#warehouseFilter', '#movementWarehouseFilter'];
                warehouseSelects.forEach(selector => {
                    const select = $(selector);
                    data.data.forEach(warehouse => {
                        select.append(`<option value="${warehouse.Gudang_code}">${warehouse.Gudang_code} - ${warehouse.Gudang_desc || ''}</option>`);
                    });
                });
            }
        })
        .catch(error => console.error('Error loading warehouses:', error));
}

function refreshStockSummary() {
    $('#stockSummaryTable').DataTable().ajax.reload();
}

function loadMovementHistory() {
    const warehouse = document.getElementById('movementWarehouseFilter').value;
    const sku = document.getElementById('movementSkuFilter').value;
    
    if (!warehouse || !sku) {
        alert('Please select warehouse and enter SKU Business');
        return;
    }
    
    fetch(`/api/wms/inventory/stock/movement?warehouse=${warehouse}&sku_business=${sku}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.querySelector('#movementHistoryTable tbody');
                tbody.innerHTML = '';
                
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No movement history found</td></tr>';
                    return;
                }
                
                data.data.forEach(item => {
                    const row = `
                        <tr>
                            <td>${item.rec_datecreated ? new Date(item.rec_datecreated).toLocaleDateString('id-ID') : '-'}</td>
                            <td>${item.tr_main_code}</td>
                            <td>${item.main_type_transaksi || '-'}</td>
                            <td>${item.main_qty_in ? parseFloat(item.main_qty_in).toLocaleString('id-ID') : '0'}</td>
                            <td>${item.main_qtu_out ? parseFloat(item.main_qtu_out).toLocaleString('id-ID') : '0'}</td>
                            <td>${item.main_stock_akhir ? parseFloat(item.main_stock_akhir).toLocaleString('id-ID') : '0'}</td>
                            <td>${item.main_inv_price ? 'Rp ' + parseFloat(item.main_inv_price).toLocaleString('id-ID') : '-'}</td>
                            <td>${item.main_inv_tracking || '-'}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                alert('Error loading movement history: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading movement history');
        });
}
</script>
@endpush