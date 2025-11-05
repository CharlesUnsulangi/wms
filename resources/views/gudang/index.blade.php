@extends('layouts.app')

@section('title', 'Manajemen Gudang')
@section('page-title', 'Manajemen Gudang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Gudang</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGudangModal">
                    <i class="fas fa-plus me-2"></i>Tambah Gudang
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="gudangTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode Gudang</th>
                                <th>Deskripsi</th>
                                <th>Business</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
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

<!-- Add Gudang Modal -->
<div class="modal fade" id="addGudangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Gudang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addGudangForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Gudang_code" class="form-label">Kode Gudang *</label>
                        <input type="text" class="form-control" id="Gudang_code" name="Gudang_code" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="Gudang_desc" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="Gudang_desc" name="Gudang_desc" maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label for="Gudang_business" class="form-label">Business</label>
                        <input type="text" class="form-control" id="Gudang_business" name="Gudang_business" maxlength="50">
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

<!-- Edit Gudang Modal -->
<div class="modal fade" id="editGudangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGudangForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_Gudang_code" name="Gudang_code">
                    <div class="mb-3">
                        <label for="edit_Gudang_desc" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="edit_Gudang_desc" name="Gudang_desc" maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label for="edit_Gudang_business" class="form-label">Business</label>
                        <input type="text" class="form-control" id="edit_Gudang_business" name="Gudang_business" maxlength="50">
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
    const table = $('#gudangTable').DataTable({
        ajax: {
            url: '/api/wms/gudang',
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'Gudang_code' },
            { data: 'Gudang_desc' },
            { data: 'Gudang_business' },
            { 
                data: 'rec_status',
                render: function(data) {
                    return data === 'A' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            { 
                data: 'rec_datecreated',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            { 
                data: 'rec_dateupdate',
                render: function(data) {
                    return data && data !== '1900-01-01T00:00:00.000Z' ? new Date(data).toLocaleDateString('id-ID') : '-';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editGudang('${row.Gudang_code}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteGudang('${row.Gudang_code}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
    });

    // Add Gudang Form Submit
    $('#addGudangForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        $.ajax({
            url: '/api/wms/gudang',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    $('#addGudangModal').modal('hide');
                    $('#addGudangForm')[0].reset();
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

    // Edit Gudang Form Submit
    $('#editGudangForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const gudangCode = data.Gudang_code;
        
        $.ajax({
            url: `/api/wms/gudang/${gudangCode}`,
            method: 'PUT',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    $('#editGudangModal').modal('hide');
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

function editGudang(gudangCode) {
    $.ajax({
        url: `/api/wms/gudang/${gudangCode}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const gudang = response.data;
                $('#edit_Gudang_code').val(gudang.Gudang_code);
                $('#edit_Gudang_desc').val(gudang.Gudang_desc);
                $('#edit_Gudang_business').val(gudang.Gudang_business);
                $('#editGudangModal').modal('show');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', response.message || 'Gagal mengambil data gudang');
        }
    });
}

function deleteGudang(gudangCode) {
    if (confirm('Apakah Anda yakin ingin menghapus gudang ini?')) {
        $.ajax({
            url: `/api/wms/gudang/${gudangCode}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $('#gudangTable').DataTable().ajax.reload();
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('danger', response.message || 'Gagal menghapus gudang');
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