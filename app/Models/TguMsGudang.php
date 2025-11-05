<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TguMsGudang extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TGU_ms_gudang';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'Gudang_code';

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
        'Gudang_code',
        'Gudang_desc',
        'Gudang_business'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime'
    ];
}
