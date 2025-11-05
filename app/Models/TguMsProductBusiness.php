<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TguMsProductBusiness extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tgu_ms_product_Business';

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['SKU_Business', 'Business'];

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
        'SKU_Business',
        'Business',
        'SKU_master',
        'SKU_NED',
        'id_mapping_tgu',
        'SKU_description',
        'SKU_brandcode',
        'SKU_convertpcs',
        'SKU_BUM',
        'SKU_statusPPN',
        'SKU_Barcode_pcs',
        'SKU_Barcode_ctn',
        'SKU_Hargajual_pcs',
        'SKU_Hargabeli_pcs',
        'SKU_Hargajual_ctn',
        'SKU_Hargabeli_ctn',
        'SKU_Status_Product',
        'SKU_width',
        'SKU_length',
        'SKU_height',
        'SKU_weight',
        'SKU_date',
        'SKU_MOP',
        'SKU_minimum_days_cover',
        'SKU_unit_of_measurement_short_name_hidden',
        'SKU_unit_of_measurement_short_name_buying',
        'SKU_unit_of_measurement_short_name_selling',
        'SKU_unit_of_measurement_short_name_receiving',
        'SKU_unit_of_measurement_short_name_physical',
        'SKU_statusvalidasi',
        'SKU_type',
        'SKU_barcode_verif',
        'SKU_barcode_status',
        'SKU_missmatch_business_code',
        'SKU_category',
        'SKU_subcategory',
        'SKU_default',
        'SKU_convertpack',
        'SKU_convert_pack'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'SKU_date' => 'datetime',
        'SKU_BUM' => 'decimal:2',
        'SKU_Hargajual_pcs' => 'decimal:2',
        'SKU_Hargabeli_pcs' => 'decimal:2',
        'SKU_Hargajual_ctn' => 'decimal:2',
        'SKU_Hargabeli_ctn' => 'decimal:2',
        'SKU_width' => 'decimal:2',
        'SKU_length' => 'decimal:2',
        'SKU_height' => 'decimal:2',
        'SKU_weight' => 'decimal:2',
        'SKU_minimum_days_cover' => 'decimal:2',
        'SKU_convertpack' => 'decimal:2'
    ];
}
