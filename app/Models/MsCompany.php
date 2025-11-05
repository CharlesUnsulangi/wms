<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsCompany extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ms_company';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'company_code';

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
        'company_code',
        'company_desc'
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

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('rec_status', 'A');
    }

    /**
     * Get inventory transactions for this company.
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(TguTrInvMainMutasi::class, 'rec_comcode', 'company_code');
    }
}
