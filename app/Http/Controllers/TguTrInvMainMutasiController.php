<?php

namespace App\Http\Controllers;

use App\Models\TguTrInvMainMutasi;
use App\Models\TguMsGudang;
use App\Models\TguMsProductBusiness;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TguTrInvMainMutasiController extends Controller
{
    /**
     * Display a listing of the resource for web view.
     */
    public function index(Request $request)
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
        return view('inventory.index');
    }
    
    /**
     * Get DataTables data with server-side processing for Inventory
     */
    private function getDataTablesData(Request $request): JsonResponse
    {
        try {
            $query = TguTrInvMainMutasi::with(['productBusiness', 'warehouse'])->active();
            
            // Apply filters
            if ($request->filled('warehouse')) {
                $query->forWarehouse($request->warehouse);
            }
            
            if ($request->filled('product')) {
                $query->forProduct($request->product);
            }
            
            if ($request->filled('transaction_type')) {
                $query->where('main_type_transaksi', $request->transaction_type);
            }
            
            // Handle search
            if ($request->filled('search') && isset($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('tr_main_code', 'like', "%{$search}%")
                      ->orWhere('main_ms_sku_business', 'like', "%{$search}%")
                      ->orWhere('main_type_transaksi', 'like', "%{$search}%")
                      ->orWhere('main_gudang_asal', 'like', "%{$search}%")
                      ->orWhere('main_gudang_tujuan', 'like', "%{$search}%");
                });
            }
            
            // Get total records
            $totalRecords = TguTrInvMainMutasi::active()->count();
            $filteredRecords = $query->count();
            
            // Handle ordering
            if ($request->filled('order')) {
                $columns = ['tr_main_code', 'main_ms_sku_business', 'main_type_transaksi', 'main_qty', 'main_gudang_asal', 'main_gudang_tujuan', 'rec_datecreated'];
                $orderColumn = $columns[$request->order[0]['column']] ?? 'rec_datecreated';
                $orderDirection = $request->order[0]['dir'] ?? 'desc';
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                $query->orderBy('rec_datecreated', 'desc');
            }
            
            // Handle pagination
            if ($request->filled('start') && $request->filled('length')) {
                $query->skip($request->start)->take($request->length);
            }
            
            $inventories = $query->get();
            
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $inventories->map(function($inventory) {
                    return [
                        'tr_main_code' => $inventory->tr_main_code,
                        'main_ms_sku_business' => $inventory->main_ms_sku_business,
                        'main_type_transaksi' => $inventory->main_type_transaksi,
                        'main_qty' => $inventory->main_qty,
                        'main_gudang_asal' => $inventory->main_gudang_asal,
                        'main_gudang_tujuan' => $inventory->main_gudang_tujuan,
                        'rec_datecreated' => $inventory->rec_datecreated,
                        'product_description' => $inventory->productBusiness->SKU_description ?? '-',
                        'actions' => $inventory->tr_main_code
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
     * API method to get inventory transactions.
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $query = TguTrInvMainMutasi::with(['productBusiness', 'warehouse'])
                                     ->active()
                                     ->orderBy('rec_datecreated', 'desc');
            
            // Add warehouse filter
            if (request()->has('warehouse')) {
                $query->forWarehouse(request()->get('warehouse'));
            }
            
            // Add product filter
            if (request()->has('product')) {
                $query->forProduct(request()->get('product'));
            }
            
            // Add transaction type filter
            if (request()->has('transaction_type')) {
                $query->where('main_type_transaksi', request()->get('transaction_type'));
            }
            
            // Add date range filter
            if (request()->has('date_from')) {
                $query->where('rec_datecreated', '>=', request()->get('date_from'));
            }
            if (request()->has('date_to')) {
                $query->where('rec_datecreated', '<=', request()->get('date_to'));
            }
            
            $transactions = $query->limit(1000)->get(); // Limit untuk performance
            
            return response()->json([
                'success' => true,
                'message' => 'Data inventory transactions berhasil diambil',
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current stock summary by warehouse and product.
     */
    public function getStockSummary(): JsonResponse
    {
        try {
            $stockSummary = DB::select("
                SELECT 
                    main_ms_inv as warehouse_code,
                    main_ms_sku_business as sku_business,
                    SUM(ISNULL(main_qty_in, 0)) as total_qty_in,
                    SUM(ISNULL(main_qtu_out, 0)) as total_qty_out,
                    MAX(main_stock_akhir) as current_stock,
                    MAX(main_inv_price) as last_price,
                    MAX(rec_datecreated) as last_transaction_date
                FROM TGU_tr_inv_main_mutasi 
                WHERE rec_status = 'A'
                GROUP BY main_ms_inv, main_ms_sku_business
                HAVING MAX(main_stock_akhir) > 0
                ORDER BY main_ms_inv, main_ms_sku_business
            ");
            
            return response()->json([
                'success' => true,
                'message' => 'Stock summary berhasil diambil',
                'data' => $stockSummary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil stock summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock movement history for specific product and warehouse.
     */
    public function getStockMovement(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'warehouse' => 'required|string',
                'sku_business' => 'required|string'
            ]);

            $movements = TguTrInvMainMutasi::where('main_ms_inv', $request->warehouse)
                                         ->where('main_ms_sku_business', $request->sku_business)
                                         ->active()
                                         ->orderBy('rec_datecreated', 'desc')
                                         ->limit(100)
                                         ->get();

            return response()->json([
                'success' => true,
                'message' => 'Stock movement history berhasil diambil',
                'data' => $movements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil stock movement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard analytics data.
     */
    public function getDashboardAnalytics(): JsonResponse
    {
        try {
            // Total transactions today
            $todayTransactions = TguTrInvMainMutasi::active()
                                                 ->whereDate('rec_datecreated', today())
                                                 ->count();

            // Stock in today
            $todayStockIn = TguTrInvMainMutasi::active()
                                            ->whereDate('rec_datecreated', today())
                                            ->sum('main_qty_in');

            // Stock out today
            $todayStockOut = TguTrInvMainMutasi::active()
                                             ->whereDate('rec_datecreated', today())
                                             ->sum('main_qtu_out');

            // Low stock items (stock < 10)
            $lowStockItems = DB::select("
                SELECT COUNT(*) as count
                FROM (
                    SELECT main_ms_inv, main_ms_sku_business, MAX(main_stock_akhir) as current_stock
                    FROM TGU_tr_inv_main_mutasi 
                    WHERE rec_status = 'A'
                    GROUP BY main_ms_inv, main_ms_sku_business
                    HAVING MAX(main_stock_akhir) < 10 AND MAX(main_stock_akhir) > 0
                ) as low_stock
            ");

            // Total active products with stock
            $activeProducts = DB::select("
                SELECT COUNT(*) as count
                FROM (
                    SELECT main_ms_inv, main_ms_sku_business
                    FROM TGU_tr_inv_main_mutasi 
                    WHERE rec_status = 'A'
                    GROUP BY main_ms_inv, main_ms_sku_business
                    HAVING MAX(main_stock_akhir) > 0
                ) as active_products
            ");

            return response()->json([
                'success' => true,
                'data' => [
                    'today_transactions' => $todayTransactions,
                    'today_stock_in' => floatval($todayStockIn),
                    'today_stock_out' => floatval($todayStockOut),
                    'low_stock_items' => $lowStockItems[0]->count ?? 0,
                    'active_products' => $activeProducts[0]->count ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil analytics data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
