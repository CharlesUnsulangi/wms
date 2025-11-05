@extends('layouts.app')

@section('title', 'Manajemen Produk Business')
@section('page-title', 'Manajemen Produk Business')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Produk Business</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </button>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="businessFilter">
                            <option value="">Semua Business</option>
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Semua Kategori</option>
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="productTable">
                        <thead class="table-dark">
                            <tr>
                                <th>SKU Business</th>
                                <th>Business</th>
                                <th>Deskripsi</th>
                                <th>Brand</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>Harga Beli</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Business Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_Business" class="form-label">SKU Business *</label>
                                <input type="text" class="form-control" id="SKU_Business" name="SKU_Business" required maxlength="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Business" class="form-label">Business *</label>
                                <input type="text" class="form-control" id="Business" name="Business" required maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_master" class="form-label">SKU Master *</label>
                                <input type="text" class="form-control" id="SKU_master" name="SKU_master" required maxlength="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_description" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="SKU_description" name="SKU_description" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_brandcode" class="form-label">Brand Code</label>
                                <input type="text" class="form-control" id="SKU_brandcode" name="SKU_brandcode" maxlength="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_category" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="SKU_category" name="SKU_category" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_subcategory" class="form-label">Sub Kategori</label>
                                <input type="text" class="form-control" id="SKU_subcategory" name="SKU_subcategory" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_convertpcs" class="form-label">Convert PCS</label>
                                <input type="text" class="form-control" id="SKU_convertpcs" name="SKU_convertpcs" value="1" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_Hargajual_pcs" class="form-label">Harga Jual (PCS)</label>
                                <input type="number" class="form-control" id="SKU_Hargajual_pcs" name="SKU_Hargajual_pcs" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SKU_Hargabeli_pcs" class="form-label">Harga Beli (PCS)</label>
                                <input type="number" class="form-control" id="SKU_Hargabeli_pcs" name="SKU_Hargabeli_pcs" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk Business</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProductForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_SKU_Business" name="SKU_Business">
                    <input type="hidden" id="edit_Business" name="Business">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">SKU Business</label>
                                <input type="text" class="form-control" id="edit_SKU_Business_display" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Business</label>
                                <input type="text" class="form-control" id="edit_Business_display" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_SKU_description" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control" id="edit_SKU_description" name="SKU_description" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_brandcode" class="form-label">Brand Code</label>
                                <input type="text" class="form-control" id="edit_SKU_brandcode" name="SKU_brandcode" maxlength="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_category" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="edit_SKU_category" name="SKU_category" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_subcategory" class="form-label">Sub Kategori</label>
                                <input type="text" class="form-control" id="edit_SKU_subcategory" name="SKU_subcategory" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_Status_Product" class="form-label">Status Produk</label>
                                <select class="form-select" id="edit_SKU_Status_Product" name="SKU_Status_Product">
                                    <option value="ACTIVE">Active</option>
                                    <option value="INACTIVE">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_Hargajual_pcs" class="form-label">Harga Jual (PCS)</label>
                                <input type="number" class="form-control" id="edit_SKU_Hargajual_pcs" name="SKU_Hargajual_pcs" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_SKU_Hargabeli_pcs" class="form-label">Harga Beli (PCS)</label>
                                <input type="number" class="form-control" id="edit_SKU_Hargabeli_pcs" name="SKU_Hargabeli_pcs" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

    // Initialize DataTable
    const table = $('#productTable').DataTable({
        ajax: {
            url: '/api/wms/product-business',
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'SKU_Business' },
            { data: 'Business' },
            { 
                data: 'SKU_description',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'SKU_brandcode',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'SKU_category',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'SKU_Hargajual_pcs',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                }
            },
            { 
                data: 'SKU_Hargabeli_pcs',
                render: function(data) {
                    return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                }
            },
            { 
                data: 'SKU_Status_Product',
                render: function(data) {
                    if (data === 'ACTIVE') {
                        return '<span class="badge bg-success">Active</span>';
                    } else if (data === 'INACTIVE') {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                    return '<span class="badge bg-secondary">Unknown</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct('${row.SKU_Business}', '${row.Business}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct('${row.SKU_Business}', '${row.Business}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        pageLength: 25
    });

    // Load filter options
    loadFilterOptions();

    // Filter event handlers
    $('#businessFilter, #categoryFilter, #statusFilter').on('change', function() {
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function() {
        $('#businessFilter, #categoryFilter, #statusFilter').val('');
        table.ajax.reload();
    });

    // Add Product Form Submit
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        $.ajax({
            url: '/api/wms/product-business',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    $('#addProductModal').modal('hide');
                    $('#addProductForm')[0].reset();
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('danger', response.message || 'Terjadi kesalahan');
            }
        });
    });

    // Edit Product Form Submit
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const skuBusiness = data.SKU_Business;
        
        $.ajax({
            url: `/api/wms/product-business/${skuBusiness}`,
            method: 'PUT',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    $('#editProductModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('danger', response.message || 'Terjadi kesalahan');
            }
        });
    });
});

function loadFilterOptions() {
    // Load business options
    $.ajax({
        url: '/api/wms/product-business',
        success: function(response) {
            if (response.success) {
                const businesses = [...new Set(response.data.map(item => item.Business))];
                const businessSelect = $('#businessFilter');
                businesses.forEach(business => {
                    businessSelect.append(`<option value="${business}">${business}</option>`);
                });

                const categories = [...new Set(response.data.map(item => item.SKU_category).filter(cat => cat))];
                const categorySelect = $('#categoryFilter');
                categories.forEach(category => {
                    categorySelect.append(`<option value="${category}">${category}</option>`);
                });
            }
        }
    });
}

function editProduct(skuBusiness, business) {
    $.ajax({
        url: `/api/wms/product-business/${skuBusiness}?business=${business}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const product = response.data;
                $('#edit_SKU_Business').val(product.SKU_Business);
                $('#edit_Business').val(product.Business);
                $('#edit_SKU_Business_display').val(product.SKU_Business);
                $('#edit_Business_display').val(product.Business);
                $('#edit_SKU_description').val(product.SKU_description);
                $('#edit_SKU_brandcode').val(product.SKU_brandcode);
                $('#edit_SKU_category').val(product.SKU_category);
                $('#edit_SKU_subcategory').val(product.SKU_subcategory);
                $('#edit_SKU_Status_Product').val(product.SKU_Status_Product);
                $('#edit_SKU_Hargajual_pcs').val(product.SKU_Hargajual_pcs);
                $('#edit_SKU_Hargabeli_pcs').val(product.SKU_Hargabeli_pcs);
                $('#editProductModal').modal('show');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', response.message || 'Gagal mengambil data produk');
        }
    });
}

function deleteProduct(skuBusiness, business) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        $.ajax({
            url: `/api/wms/product-business/${skuBusiness}?business=${business}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('danger', response.message || 'Gagal menghapus produk');
            }
        });
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.container-fluid.px-4').prepend(alertHtml);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}
</script>
@endpush