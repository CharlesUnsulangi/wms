<?php

namespace App\Http\Controllers;

use App\Models\TguTrInvMainMutasi;
use App\Models\TguMsGudang;
use App\Models\TguMsProductBusiness;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TguTrInvMainMutasiController extends Controller
{
    /**
     * Display a listing of the resource for web view.
     */
    public function index()
    {
        // Check if request is API
        if (request()->expectsJson() || request()->is('api/*')) {
            return $this->apiIndex();
        }
        
        // Return web view
        return view('inventory.index');
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
