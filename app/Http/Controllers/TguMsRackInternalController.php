<?php

namespace App\Http\Controllers;

use App\Models\TguMsRackInternal;
use App\Models\TguMsGudang;
use App\Models\TguMsProductBusiness;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class TguMsRackInternalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        // Handle DataTables server-side processing
        if ($request->ajax()) {
            return $this->getDataTablesData($request);
        }

        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->getApiData($request);
        }

        // Return web view
        $gudangs = TguMsGudang::active()->get();
        $businesses = TguMsProductBusiness::select('Business')->distinct()->get();
        $rackTypes = TguMsRackInternal::getRackTypes();

        return view('rack-internal.index', compact('gudangs', 'businesses', 'rackTypes'));
    }

    /**
     * Get DataTables data with server-side processing
     */
    private function getDataTablesData(Request $request): JsonResponse
    {
        try {
            $query = TguMsRackInternal::with(['gudang', 'productBusiness'])->active();

            // Apply filters
            if ($request->filled('business')) {
                $query->byBusiness($request->business);
            }

            if ($request->filled('branch')) {
                $query->byBranch($request->branch);
            }

            if ($request->filled('type')) {
                $query->byType($request->type);
            }

            // Handle search
            if ($request->filled('search') && isset($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('rack_internal_code', 'like', "%{$search}%")
                      ->orWhere('rack_principal_code', 'like', "%{$search}%")
                      ->orWhere('rack_business', 'like', "%{$search}%")
                      ->orWhere('rack_branch', 'like', "%{$search}%");
                });
            }

            // Get total records
            $totalRecords = TguMsRackInternal::active()->count();
            $filteredRecords = $query->count();

            // Handle ordering
            if ($request->filled('order')) {
                $columns = ['rack_internal_code', 'rack_principal_code', 'rack_business', 'rack_branch', 'rack_type', 'rack_active', 'rack_locked', 'rec_datecreated'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'rack_internal_code';
                $orderDirection = $request->order[0]['dir'] ?? 'asc';
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                $query->orderBy('rack_internal_code');
            }

            // Handle pagination
            if ($request->filled('start') && $request->filled('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $racks = $query->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $racks->map(function($rack) {
                    return [
                        'rack_internal_code' => $rack->rack_internal_code,
                        'rack_principal_code' => $rack->rack_principal_code,
                        'rack_business' => $rack->rack_business,
                        'rack_branch' => $rack->rack_branch,
                        'rack_type' => $rack->rack_type,
                        'rack_type_name' => $rack->rack_type_name,
                        'rack_active' => $rack->rack_active,
                        'rack_locked' => $rack->rack_locked,
                        'is_active' => $rack->is_active,
                        'is_locked' => $rack->is_locked,
                        'rec_datecreated' => $rack->rec_datecreated,
                        'actions' => [
                            'code' => $rack->rack_internal_code,
                            'branch' => $rack->rack_branch
                        ]
                    ];
                })
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'draw' => intval($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get API data for non-DataTables requests
     */
    private function getApiData(Request $request): JsonResponse
    {
        try {
            $query = TguMsRackInternal::with(['gudang', 'productBusiness'])
                ->active()
                ->orderBy('rack_internal_code');

            // Apply filters
            if ($request->filled('business')) {
                $query->byBusiness($request->business);
            }

            if ($request->filled('branch')) {
                $query->byBranch($request->branch);
            }

            if ($request->filled('type')) {
                $query->byType($request->type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('rack_internal_code', 'like', "%{$search}%")
                      ->orWhere('rack_principal_code', 'like', "%{$search}%")
                      ->orWhere('rack_business', 'like', "%{$search}%")
                      ->orWhere('rack_branch', 'like', "%{$search}%");
                });
            }

            $racks = $query->get();
            return response()->json([
                'success' => true,
                'data' => $racks->map(function($rack) {
                    return [
                        'rack_internal_code' => $rack->rack_internal_code,
                        'rack_principal_code' => $rack->rack_principal_code,
                        'rack_business' => $rack->rack_business,
                        'rack_branch' => $rack->rack_branch,
                        'rack_type' => $rack->rack_type,
                        'rack_type_name' => $rack->rack_type_name,
                        'rack_active' => $rack->rack_active,
                        'rack_locked' => $rack->rack_locked,
                        'is_active' => $rack->is_active,
                        'is_locked' => $rack->is_locked,
                        'full_rack_code' => $rack->full_rack_code,
                        'gudang' => $rack->gudang,
                        'product_business' => $rack->productBusiness,
                        'rec_datecreated' => $rack->rec_datecreated,
                        'rec_dateupdate' => $rack->rec_dateupdate,
                    ];
                })
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $gudangs = TguMsGudang::active()->get();
        $businesses = TguMsProductBusiness::select('Business')->distinct()->get();
        $rackTypes = TguMsRackInternal::getRackTypes();
        
        return view('rack-internal.create', compact('gudangs', 'businesses', 'rackTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rack_internal_code' => 'required|string|max:50',
                'rack_principal_code' => 'required|string|max:50',
                'rack_business' => 'required|string|max:50',
                'rack_branch' => 'required|string|max:50',
                'rack_type' => 'required|string|in:001,002',
                'rack_active' => 'required|string|in:0,1',
                'rack_locked' => 'required|string|in:0,1',
            ]);

            // Check for duplicate composite key
            $exists = TguMsRackInternal::where('rack_internal_code', $validated['rack_internal_code'])
                ->where('rack_branch', $validated['rack_branch'])
                ->where('rec_status', '1')
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rack with this internal code and branch already exists.'
                ], 422);
            }

            $validated['rec_usercreated'] = auth()->user()->name ?? 'system';
            $validated['rec_datecreated'] = now();
            $validated['rec_status'] = '1';

            $rack = TguMsRackInternal::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Rack created successfully',
                'data' => $rack
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $rackCode, string $branch): JsonResponse
    {
        try {
            $rack = TguMsRackInternal::with(['gudang', 'productBusiness'])
                ->where('rack_internal_code', $rackCode)
                ->where('rack_branch', $branch)
                ->where('rec_status', '1')
                ->first();

            if (!$rack) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rack not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'rack_internal_code' => $rack->rack_internal_code,
                    'rack_principal_code' => $rack->rack_principal_code,
                    'rack_business' => $rack->rack_business,
                    'rack_branch' => $rack->rack_branch,
                    'rack_type' => $rack->rack_type,
                    'rack_type_name' => $rack->rack_type_name,
                    'rack_active' => $rack->rack_active,
                    'rack_locked' => $rack->rack_locked,
                    'is_active' => $rack->is_active,
                    'is_locked' => $rack->is_locked,
                    'full_rack_code' => $rack->full_rack_code,
                    'gudang' => $rack->gudang,
                    'product_business' => $rack->productBusiness,
                    'rec_datecreated' => $rack->rec_datecreated,
                    'rec_dateupdate' => $rack->rec_dateupdate,
                    'rec_usercreated' => $rack->rec_usercreated,
                    'rec_userupdate' => $rack->rec_userupdate,
                ]
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $rackCode, string $branch): View
    {
        $rack = TguMsRackInternal::where('rack_internal_code', $rackCode)
            ->where('rack_branch', $branch)
            ->where('rec_status', '1')
            ->firstOrFail();

        $gudangs = TguMsGudang::active()->get();
        $businesses = TguMsProductBusiness::select('Business')->distinct()->get();
        $rackTypes = TguMsRackInternal::getRackTypes();
        
        return view('rack-internal.edit', compact('rack', 'gudangs', 'businesses', 'rackTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $rackCode, string $branch): JsonResponse
    {
        try {
            $rack = TguMsRackInternal::where('rack_internal_code', $rackCode)
                ->where('rack_branch', $branch)
                ->where('rec_status', '1')
                ->first();

            if (!$rack) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rack not found'
                ], 404);
            }

            $validated = $request->validate([
                'rack_principal_code' => 'required|string|max:50',
                'rack_business' => 'required|string|max:50',
                'rack_type' => 'required|string|in:001,002',
                'rack_active' => 'required|string|in:0,1',
                'rack_locked' => 'required|string|in:0,1',
            ]);

            $validated['rec_userupdate'] = auth()->user()->name ?? 'system';
            $validated['rec_dateupdate'] = now();

            $rack->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Rack updated successfully',
                'data' => $rack->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $rackCode, string $branch): JsonResponse
    {
        try {
            $rack = TguMsRackInternal::where('rack_internal_code', $rackCode)
                ->where('rack_branch', $branch)
                ->where('rec_status', '1')
                ->first();

            if (!$rack) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rack not found'
                ], 404);
            }

            // Soft delete by setting rec_status to '0'
            $rack->update([
                'rec_status' => '0',
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rack deleted successfully'
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get racks by business
     */
    public function getByBusiness(string $business): JsonResponse
    {
        try {
            $racks = TguMsRackInternal::active()
                ->byBusiness($business)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $racks
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get racks by branch
     */
    public function getByBranch(string $branch): JsonResponse
    {
        try {
            $racks = TguMsRackInternal::active()
                ->byBranch($branch)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $racks
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rack statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_racks' => TguMsRackInternal::active()->count(),
                'regular_racks' => TguMsRackInternal::active()->regular()->count(),
                'ned_racks' => TguMsRackInternal::active()->ned()->count(),
                'locked_racks' => TguMsRackInternal::active()->where('rack_locked', '1')->count(),
                'unlocked_racks' => TguMsRackInternal::active()->unlocked()->count(),
                'by_business' => TguMsRackInternal::active()
                    ->select('rack_business')
                    ->selectRaw('count(*) as count')
                    ->groupBy('rack_business')
                    ->get(),
                'by_branch' => TguMsRackInternal::active()
                    ->select('rack_branch')
                    ->selectRaw('count(*) as count')
                    ->groupBy('rack_branch')
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }
}
