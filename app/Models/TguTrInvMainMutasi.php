<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TguTrInvMainMutasi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TGU_tr_inv_main_mutasi';

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['tr_main_code', 'tr_main_code_inv', 'main_ms_inv', 'main_no', 'main_ms_sku_business'];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
        'rec_comcode',
        'rec_areacode',
        'tr_main_code',
        'tr_main_code_inv',
        'main_ms_inv',
        'main_no',
        'main_qty_in',
        'main_qtu_out',
        'main_stock_akhir',
        'main_type_transaksi',
        'main_inv_unit',
        'main_inv_price',
        'main_qty_return',
        'main_qty_bekas',
        'main_inv_tracking',
        'main_inv_daterack',
        'main_inv_tr_rack',
        'main_inv_status',
        'main_ms_sku_supplier',
        'main_ms_sku_business',
        'cogs',
        'cogs_last',
        'main_client',
        'main_ms_sku_business_order',
        'main_qty_conversi'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'main_inv_daterack' => 'datetime',
        'main_qty_in' => 'decimal:2',
        'main_qtu_out' => 'decimal:2',
        'main_stock_akhir' => 'decimal:2',
        'main_inv_price' => 'decimal:2',
        'main_qty_return' => 'decimal:2',
        'main_qty_bekas' => 'decimal:2',
        'cogs' => 'decimal:2',
        'cogs_last' => 'decimal:2',
        'main_qty_conversi' => 'decimal:2'
    ];

    /**
     * Get the product business that owns the inventory mutation.
     */
    public function productBusiness(): BelongsTo
    {
        return $this->belongsTo(TguMsProductBusiness::class, 'main_ms_sku_business', 'SKU_Business');
    }

    /**
     * Get the warehouse that owns the inventory mutation.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(TguMsGudang::class, 'main_ms_inv', 'Gudang_code');
    }

    /**
     * Get the company that owns the inventory mutation.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MsCompany::class, 'rec_comcode', 'company_code');
    }

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', 'A');
    }

    /**
     * Scope for specific warehouse
     */
    public function scopeForWarehouse($query, $warehouseCode)
    {
        return $query->where('main_ms_inv', $warehouseCode);
    }

    /**
     * Scope for specific product
     */
    public function scopeForProduct($query, $skuBusiness)
    {
        return $query->where('main_ms_sku_business', $skuBusiness);
    }

    /**
     * Scope for stock in transactions
     */
    public function scopeStockIn($query)
    {
        return $query->where('main_qty_in', '>', 0);
    }

    /**
     * Scope for stock out transactions
     */
    public function scopeStockOut($query)
    {
        return $query->where('main_qtu_out', '>', 0);
    }
}
