<?php

namespace App\Http\Controllers;

use App\Models\TguMsGudang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class TguMsGudangController extends Controller
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
        
        // Check if request is API
        if (request()->expectsJson() || request()->is('api/*')) {
            return $this->apiIndex();
        }
        
        // Return web view
        return view('gudang.index');
    }
    
    /**
     * Get DataTables data with server-side processing
     */
    private function getDataTablesData(Request $request): JsonResponse
    {
        try {
            $query = TguMsGudang::where('rec_status', 'A');
            
            // Apply filters
            if ($request->filled('business')) {
                $query->where('Business', $request->business);
            }
            
            // Handle search
            if ($request->filled('search') && isset($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('Gudang_code', 'like', "%{$search}%")
                      ->orWhere('Gudang_name', 'like', "%{$search}%")
                      ->orWhere('Business', 'like', "%{$search}%")
                      ->orWhere('Gudang_address', 'like', "%{$search}%");
                });
            }
            
            // Get total records
            $totalRecords = TguMsGudang::where('rec_status', 'A')->count();
            $filteredRecords = $query->count();
            
            // Handle ordering
            if ($request->filled('order')) {
                $columns = ['Gudang_code', 'Gudang_name', 'Business', 'Gudang_address', 'Gudang_status'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'Gudang_code';
                $orderDirection = $request->order[0]['dir'] ?? 'asc';
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                $query->orderBy('Gudang_code');
            }
            
            // Handle pagination
            if ($request->filled('start') && $request->filled('length')) {
                $query->skip($request->start)->take($request->length);
            }
            
            $gudangs = $query->get();
            
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $gudangs->map(function($gudang) {
                    return [
                        'Gudang_code' => $gudang->Gudang_code,
                        'Gudang_name' => $gudang->Gudang_name,
                        'Business' => $gudang->Business,
                        'Gudang_address' => $gudang->Gudang_address ?? '-',
                        'Gudang_status' => $gudang->Gudang_status,
                        'actions' => $gudang->Gudang_code
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
     * API method to get listing of gudang.
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $gudangs = TguMsGudang::where('rec_status', 'A')
                                 ->orderBy('Gudang_code')
                                 ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data gudang berhasil diambil',
                'data' => $gudangs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // API-based, form will be handled by modal in index view
        return redirect()->route('gudang.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'Gudang_code' => 'required|string|max:50|unique:TGU_ms_gudang,Gudang_code',
                'Gudang_desc' => 'nullable|string|max:200',
                'Gudang_business' => 'nullable|string|max:50'
            ]);

            $gudang = TguMsGudang::create([
                'rec_usercreated' => auth()->user()->name ?? 'system',
                'rec_userupdate' => '',
                'rec_datecreated' => now(),
                'rec_dateupdate' => new \DateTime('1900-01-01'),
                'rec_status' => 'A',
                'Gudang_code' => $request->Gudang_code,
                'Gudang_desc' => $request->Gudang_desc,
                'Gudang_business' => $request->Gudang_business
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil ditambahkan',
                'data' => $gudang
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $gudang = TguMsGudang::where('Gudang_code', $id)
                                ->where('rec_status', 'A')
                                ->first();
            
            if (!$gudang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gudang tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data gudang berhasil diambil',
                'data' => $gudang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // API-based, edit form will be handled by modal in index view
        return redirect()->route('gudang.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'Gudang_desc' => 'nullable|string|max:200',
                'Gudang_business' => 'nullable|string|max:50'
            ]);

            $gudang = TguMsGudang::where('Gudang_code', $id)->first();
            
            if (!$gudang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gudang tidak ditemukan'
                ], 404);
            }

            $gudang->update([
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
                'Gudang_desc' => $request->Gudang_desc,
                'Gudang_business' => $request->Gudang_business
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil diupdate',
                'data' => $gudang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $gudang = TguMsGudang::where('Gudang_code', $id)->first();
            
            if (!$gudang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gudang tidak ditemukan'
                ], 404);
            }

            // Soft delete dengan mengubah status menjadi 'D'
            $gudang->update([
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
                'rec_status' => 'D'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gudang: ' . $e->getMessage()
            ], 500);
        }
    }
}
