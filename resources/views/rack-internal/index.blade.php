@extends('layouts.app')

@section('title', 'Rack Internal Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-warehouse text-primary"></i>
                    Rack Internal Management
                </h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRackModal">
                    <i class="fas fa-plus"></i> Add New Rack
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Racks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRacks">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Regular Racks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="regularRacks">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">NED Racks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="nedRacks">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Locked Racks</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="lockedRacks">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-lock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> Filters
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="businessFilter" class="form-label">Business</label>
                            <select class="form-select" id="businessFilter">
                                <option value="">All Business</option>
                                @foreach($businesses as $business)
                                <option value="{{ $business->Business }}">{{ $business->Business }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="branchFilter" class="form-label">Branch/Gudang</label>
                            <select class="form-select" id="branchFilter">
                                <option value="">All Branches</option>
                                @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->Gudang_code }}">{{ $gudang->Gudang_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Rack Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                @foreach($rackTypes as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchFilter" class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search racks...">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="applyFilters">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-secondary" id="clearFilters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rack Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Rack Internal List
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="rackTable" width="100%" cellspacing="0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Internal Code</th>
                                    <th>Principal Code</th>
                                    <th>Business</th>
                                    <th>Branch</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Lock Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rack Modal -->
<div class="modal fade" id="addRackModal" tabindex="-1" aria-labelledby="addRackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRackModalLabel">
                    <i class="fas fa-plus text-primary"></i> Add New Rack
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRackForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rack_internal_code" class="form-label">Internal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rack_internal_code" name="rack_internal_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rack_principal_code" class="form-label">Principal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rack_principal_code" name="rack_principal_code" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rack_business" class="form-label">Business <span class="text-danger">*</span></label>
                                <select class="form-select" id="rack_business" name="rack_business" required>
                                    <option value="">Select Business</option>
                                    @foreach($businesses as $business)
                                    <option value="{{ $business->Business }}">{{ $business->Business }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rack_branch" class="form-label">Branch/Gudang <span class="text-danger">*</span></label>
                                <select class="form-select" id="rack_branch" name="rack_branch" required>
                                    <option value="">Select Branch</option>
                                    @foreach($gudangs as $gudang)
                                    <option value="{{ $gudang->Gudang_code }}">{{ $gudang->Gudang_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rack_type" class="form-label">Rack Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="rack_type" name="rack_type" required>
                                    @foreach($rackTypes as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rack_active" class="form-label">Active Status</label>
                                <select class="form-select" id="rack_active" name="rack_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rack_locked" class="form-label">Lock Status</label>
                                <select class="form-select" id="rack_locked" name="rack_locked">
                                    <option value="0">Unlocked</option>
                                    <option value="1">Locked</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Rack
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rack Modal -->
<div class="modal fade" id="editRackModal" tabindex="-1" aria-labelledby="editRackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRackModalLabel">
                    <i class="fas fa-edit text-warning"></i> Edit Rack
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRackForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_rack_internal_code" name="rack_internal_code">
                    <input type="hidden" id="edit_rack_branch" name="rack_branch">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_rack_principal_code" class="form-label">Principal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_rack_principal_code" name="rack_principal_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_rack_business" class="form-label">Business <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_rack_business" name="rack_business" required>
                                    <option value="">Select Business</option>
                                    @foreach($businesses as $business)
                                    <option value="{{ $business->Business }}">{{ $business->Business }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_rack_type" class="form-label">Rack Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_rack_type" name="rack_type" required>
                                    @foreach($rackTypes as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_rack_active" class="form-label">Active Status</label>
                                <select class="form-select" id="edit_rack_active" name="rack_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_rack_locked" class="form-label">Lock Status</label>
                                <select class="form-select" id="edit_rack_locked" name="rack_locked">
                                    <option value="0">Unlocked</option>
                                    <option value="1">Locked</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Rack
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let table;
    
    // Initialize DataTable
    function initDataTable() {
        if (table) {
            table.destroy();
        }
        
        table = $('#rackTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/rack-internal',
                data: function(d) {
                    d.business = $('#businessFilter').val();
                    d.branch = $('#branchFilter').val();
                    d.type = $('#typeFilter').val();
                    d.search = $('#searchFilter').val();
                }
            },
            columns: [
                { data: 'rack_internal_code', name: 'rack_internal_code' },
                { data: 'rack_principal_code', name: 'rack_principal_code' },
                { data: 'rack_business', name: 'rack_business' },
                { data: 'rack_branch', name: 'rack_branch' },
                { 
                    data: 'rack_type_name', 
                    name: 'rack_type',
                    render: function(data, type, row) {
                        const typeClass = row.rack_type === '001' ? 'success' : 'info';
                        return `<span class="badge bg-${typeClass}">${data}</span>`;
                    }
                },
                { 
                    data: 'is_active', 
                    name: 'rack_active',
                    render: function(data, type, row) {
                        return data ? 
                            '<span class="badge bg-success">Active</span>' : 
                            '<span class="badge bg-danger">Inactive</span>';
                    }
                },
                { 
                    data: 'is_locked', 
                    name: 'rack_locked',
                    render: function(data, type, row) {
                        return data ? 
                            '<span class="badge bg-warning"><i class="fas fa-lock"></i> Locked</span>' : 
                            '<span class="badge bg-success"><i class="fas fa-unlock"></i> Unlocked</span>';
                    }
                },
                { 
                    data: 'rec_datecreated', 
                    name: 'rec_datecreated',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info view-rack" 
                                        data-code="${row.rack_internal_code}" 
                                        data-branch="${row.rack_branch}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning edit-rack" 
                                        data-code="${row.rack_internal_code}" 
                                        data-branch="${row.rack_branch}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-rack" 
                                        data-code="${row.rack_internal_code}" 
                                        data-branch="${row.rack_branch}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'asc']],
            pageLength: 25,
            responsive: true,
            language: {
                processing: "Loading racks...",
                emptyTable: "No racks found"
            }
        });
    }

    // Load statistics
    function loadStatistics() {
        $.get('/api/rack-internal/statistics')
        .done(function(response) {
            if (response.success) {
                $('#totalRacks').text(response.data.total_racks);
                $('#regularRacks').text(response.data.regular_racks);
                $('#nedRacks').text(response.data.ned_racks);
                $('#lockedRacks').text(response.data.locked_racks);
            }
        })
        .fail(function(xhr) {
            console.error('Failed to load statistics:', xhr.responseJSON);
        });
    }

    // Initialize page
    initDataTable();
    loadStatistics();

    // Filter handlers
    $('#applyFilters').click(function() {
        table.ajax.reload();
    });

    $('#clearFilters').click(function() {
        $('#businessFilter').val('');
        $('#branchFilter').val('');
        $('#typeFilter').val('');
        $('#searchFilter').val('');
        table.ajax.reload();
    });

    // Add rack form handler
    $('#addRackForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.post('/api/rack-internal', data)
        .done(function(response) {
            if (response.success) {
                $('#addRackModal').modal('hide');
                $('#addRackForm')[0].reset();
                table.ajax.reload();
                loadStatistics();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message
                });
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            let errorMessage = 'Failed to create rack.';
            
            if (response && response.errors) {
                errorMessage = Object.values(response.errors).flat().join('\n');
            } else if (response && response.message) {
                errorMessage = response.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage
            });
        });
    });

    // Edit rack handler
    $(document).on('click', '.edit-rack', function() {
        const rackCode = $(this).data('code');
        const branch = $(this).data('branch');
        
        $.get(`/api/rack-internal/${rackCode}/${branch}`)
        .done(function(response) {
            if (response.success) {
                const rack = response.data;
                $('#edit_rack_internal_code').val(rack.rack_internal_code);
                $('#edit_rack_branch').val(rack.rack_branch);
                $('#edit_rack_principal_code').val(rack.rack_principal_code);
                $('#edit_rack_business').val(rack.rack_business);
                $('#edit_rack_type').val(rack.rack_type);
                $('#edit_rack_active').val(rack.rack_active);
                $('#edit_rack_locked').val(rack.rack_locked);
                $('#editRackModal').modal('show');
            }
        })
        .fail(function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load rack data.'
            });
        });
    });

    // Update rack form handler
    $('#editRackForm').submit(function(e) {
        e.preventDefault();
        
        const rackCode = $('#edit_rack_internal_code').val();
        const branch = $('#edit_rack_branch').val();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: `/api/rack-internal/${rackCode}/${branch}`,
            method: 'PUT',
            data: data
        })
        .done(function(response) {
            if (response.success) {
                $('#editRackModal').modal('hide');
                table.ajax.reload();
                loadStatistics();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message
                });
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            let errorMessage = 'Failed to update rack.';
            
            if (response && response.errors) {
                errorMessage = Object.values(response.errors).flat().join('\n');
            } else if (response && response.message) {
                errorMessage = response.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage
            });
        });
    });

    // Delete rack handler
    $(document).on('click', '.delete-rack', function() {
        const rackCode = $(this).data('code');
        const branch = $(this).data('branch');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete rack ${rackCode} - ${branch}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: `/api/rack-internal/${rackCode}/${branch}`,
                    method: 'DELETE'
                })
                .done(function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        loadStatistics();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message
                        });
                    }
                })
                .fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete rack.'
                    });
                });
            }
        });
    });

    // View rack handler
    $(document).on('click', '.view-rack', function() {
        const rackCode = $(this).data('code');
        const branch = $(this).data('branch');
        
        $.get(`/api/rack-internal/${rackCode}/${branch}`)
        .done(function(response) {
            if (response.success) {
                const rack = response.data;
                const lockIcon = rack.is_locked ? '<i class="fas fa-lock text-warning"></i>' : '<i class="fas fa-unlock text-success"></i>';
                const statusBadge = rack.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                const typeBadge = rack.rack_type === '001' ? '<span class="badge bg-success">Regular</span>' : '<span class="badge bg-info">NED</span>';
                
                Swal.fire({
                    title: `Rack Details: ${rack.full_rack_code}`,
                    html: `
                        <div class="text-left">
                            <p><strong>Internal Code:</strong> ${rack.rack_internal_code}</p>
                            <p><strong>Principal Code:</strong> ${rack.rack_principal_code}</p>
                            <p><strong>Business:</strong> ${rack.rack_business}</p>
                            <p><strong>Branch:</strong> ${rack.rack_branch}</p>
                            <p><strong>Type:</strong> ${typeBadge}</p>
                            <p><strong>Status:</strong> ${statusBadge}</p>
                            <p><strong>Lock Status:</strong> ${lockIcon} ${rack.is_locked ? 'Locked' : 'Unlocked'}</p>
                            <p><strong>Created:</strong> ${new Date(rack.rec_datecreated).toLocaleString()}</p>
                            <p><strong>Updated:</strong> ${rack.rec_dateupdate ? new Date(rack.rec_dateupdate).toLocaleString() : '-'}</p>
                        </div>
                    `,
                    confirmButtonText: 'Close'
                });
            }
        })
        .fail(function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load rack details.'
            });
        });
    });
});
</script>
@endsection