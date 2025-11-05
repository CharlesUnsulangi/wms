<?php

namespace App\Http\Controllers;

use App\Models\TguMsProductBusiness;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TguMsProductBusinessController extends Controller
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
        
        // Return web view
        return view('product-business.index');
    }
    
    /**
     * Get DataTables data with server-side processing
     */
    private function getDataTablesData(Request $request): JsonResponse
    {
        try {
            $query = TguMsProductBusiness::where('rec_status', 'A');
            
            // Apply filters
            if ($request->filled('business')) {
                $query->where('Business', $request->business);
            }
            
            if ($request->filled('category')) {
                $query->where('SKU_category', 'like', "%{$request->category}%");
            }
            
            if ($request->filled('status')) {
                $query->where('SKU_Status_Product', $request->status);
            }
            
            // Handle search
            if ($request->filled('search') && isset($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('SKU_Business', 'like', "%{$search}%")
                      ->orWhere('SKU_description', 'like', "%{$search}%")
                      ->orWhere('SKU_brandcode', 'like', "%{$search}%")
                      ->orWhere('Business', 'like', "%{$search}%")
                      ->orWhere('SKU_category', 'like', "%{$search}%");
                });
            }
            
            // Get total records before filtering
            $totalRecords = TguMsProductBusiness::where('rec_status', 'A')->count();
            
            // Get filtered count
            $filteredRecords = $query->count();
            
            // Handle ordering
            if ($request->filled('order')) {
                $columns = ['SKU_Business', 'Business', 'SKU_description', 'SKU_brandcode', 'SKU_category', 'SKU_Hargajual_pcs', 'SKU_Hargabeli_pcs', 'SKU_Status_Product'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'SKU_Business';
                $orderDirection = $request->order[0]['dir'] ?? 'asc';
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                $query->orderBy('SKU_Business');
            }
            
            // Handle pagination
            if ($request->filled('start') && $request->filled('length')) {
                $query->skip($request->start)->take($request->length);
            }
            
            $products = $query->get();
            
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $products->map(function($product) {
                    return [
                        'SKU_Business' => $product->SKU_Business,
                        'Business' => $product->Business,
                        'SKU_description' => $product->SKU_description ?? '-',
                        'SKU_brandcode' => $product->SKU_brandcode ?? '-',
                        'SKU_category' => $product->SKU_category ?? '-',
                        'SKU_Hargajual_pcs' => $product->SKU_Hargajual_pcs,
                        'SKU_Hargabeli_pcs' => $product->SKU_Hargabeli_pcs,
                        'SKU_Status_Product' => $product->SKU_Status_Product,
                        'actions' => [
                            'sku' => $product->SKU_Business,
                            'business' => $product->Business
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
     * API method to get listing of product business.
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $query = TguMsProductBusiness::where('rec_status', 'A');
            
            // Add search functionality
            if (request()->has('search')) {
                $search = request()->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('SKU_Business', 'like', "%{$search}%")
                      ->orWhere('SKU_description', 'like', "%{$search}%")
                      ->orWhere('SKU_brandcode', 'like', "%{$search}%")
                      ->orWhere('Business', 'like', "%{$search}%");
                });
            }
            
            // Add business filter
            if (request()->has('business')) {
                $query->where('Business', request()->get('business'));
            }
            
            // Add category filter
            if (request()->has('category')) {
                $query->where('SKU_category', request()->get('category'));
            }
            
            $products = $query->orderBy('SKU_Business')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data product business berhasil diambil',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data product business: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // API-based, form will be handled by modal in index view
        return redirect()->route('product-business.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'SKU_Business' => 'required|string|max:200',
                'Business' => 'required|string|max:50',
                'SKU_master' => 'required|string|max:200',
                'SKU_description' => 'nullable|string|max:200',
                'SKU_brandcode' => 'nullable|string|max:200',
                'SKU_category' => 'nullable|string|max:50',
                'SKU_subcategory' => 'nullable|string|max:50',
                'SKU_Hargajual_pcs' => 'nullable|numeric',
                'SKU_Hargabeli_pcs' => 'nullable|numeric'
            ]);

            // Check if combination already exists
            $exists = TguMsProductBusiness::where('SKU_Business', $request->SKU_Business)
                                        ->where('Business', $request->Business)
                                        ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kombinasi SKU Business dan Business sudah ada'
                ], 422);
            }

            $product = TguMsProductBusiness::create([
                'rec_usercreated' => auth()->user()->name ?? 'system',
                'rec_userupdate' => '',
                'rec_datecreated' => now(),
                'rec_dateupdate' => new \DateTime('1900-01-01'),
                'rec_status' => 'A',
                'SKU_Business' => $request->SKU_Business,
                'Business' => $request->Business,
                'SKU_master' => $request->SKU_master,
                'SKU_description' => $request->SKU_description,
                'SKU_brandcode' => $request->SKU_brandcode,
                'SKU_category' => $request->SKU_category,
                'SKU_subcategory' => $request->SKU_subcategory,
                'SKU_Hargajual_pcs' => $request->SKU_Hargajual_pcs,
                'SKU_Hargabeli_pcs' => $request->SKU_Hargabeli_pcs,
                'SKU_convertpcs' => $request->SKU_convertpcs ?? '1',
                'SKU_statusPPN' => $request->SKU_statusPPN ?? 'Y',
                'SKU_Status_Product' => 'ACTIVE'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product business berhasil ditambahkan',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan product business: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $skuBusiness, string $business = null): JsonResponse
    {
        try {
            // For composite key, we need both SKU_Business and Business
            if (!$business && request()->has('business')) {
                $business = request()->get('business');
            }
            
            $product = TguMsProductBusiness::where('SKU_Business', $skuBusiness)
                                         ->where('Business', $business)
                                         ->where('rec_status', 'A')
                                         ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product business tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data product business berhasil diambil',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data product business: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // API-based, edit form will be handled by modal in index view
        return redirect()->route('product-business.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $skuBusiness): JsonResponse
    {
        try {
            $request->validate([
                'Business' => 'required|string|max:50',
                'SKU_description' => 'nullable|string|max:200',
                'SKU_brandcode' => 'nullable|string|max:200',
                'SKU_category' => 'nullable|string|max:50',
                'SKU_subcategory' => 'nullable|string|max:50',
                'SKU_Hargajual_pcs' => 'nullable|numeric',
                'SKU_Hargabeli_pcs' => 'nullable|numeric',
                'SKU_Status_Product' => 'nullable|string|max:50'
            ]);

            $product = TguMsProductBusiness::where('SKU_Business', $skuBusiness)
                                         ->where('Business', $request->Business)
                                         ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product business tidak ditemukan'
                ], 404);
            }

            $product->update([
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
                'SKU_description' => $request->SKU_description,
                'SKU_brandcode' => $request->SKU_brandcode,
                'SKU_category' => $request->SKU_category,
                'SKU_subcategory' => $request->SKU_subcategory,
                'SKU_Hargajual_pcs' => $request->SKU_Hargajual_pcs,
                'SKU_Hargabeli_pcs' => $request->SKU_Hargabeli_pcs,
                'SKU_Status_Product' => $request->SKU_Status_Product ?? $product->SKU_Status_Product
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product business berhasil diupdate',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate product business: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $skuBusiness): JsonResponse
    {
        try {
            $business = request()->get('business');
            
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter business diperlukan'
                ], 400);
            }
            
            $product = TguMsProductBusiness::where('SKU_Business', $skuBusiness)
                                         ->where('Business', $business)
                                         ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product business tidak ditemukan'
                ], 404);
            }

            // Soft delete dengan mengubah status menjadi 'D'
            $product->update([
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
                'rec_status' => 'D'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product business berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus product business: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get products by category.
     */
    public function getByCategory(string $category): JsonResponse
    {
        try {
            $products = TguMsProductBusiness::where('SKU_category', $category)
                                          ->where('rec_status', 'A')
                                          ->orderBy('SKU_Business')
                                          ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data product berhasil diambil',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data product: ' . $e->getMessage()
            ], 500);
        }
    }
}
