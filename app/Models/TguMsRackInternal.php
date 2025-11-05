<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TguMsRackInternal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tgu_ms_rack_internal';
    
    // Composite primary key
    protected $primaryKey = ['rack_internal_code', 'rack_branch'];
    public $incrementing = false;
    protected $keyType = 'string';

    // Timestamps
    const CREATED_AT = 'rec_datecreated';
    const UPDATED_AT = 'rec_dateupdate';
    const DELETED_AT = 'rec_status'; // Using rec_status for soft delete (0 = deleted, 1 = active)

    protected $fillable = [
        'rack_internal_code',
        'rack_principal_code',
        'rack_business',
        'rack_branch',
        'rack_type',
        'rack_active',
        'rack_locked',
        'rec_usercreated',
        'rec_userupdate',
    ];

    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'rack_active' => 'string',
        'rack_locked' => 'string',
        'rec_status' => 'string',
        'rack_type' => 'string',
    ];

    protected $attributes = [
        'rec_status' => '1',
        'rack_active' => '1',
        'rack_locked' => '0',
        'rack_type' => '001', // Default to Regular rack
    ];

    // Override the default soft delete query
    protected function performDeleteOnModel()
    {
        $this->{$this->getDeletedAtColumn()} = '0';
        return $this->save();
    }

    // Override restore method
    public function restore()
    {
        $this->{$this->getDeletedAtColumn()} = '1';
        return $this->save();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('rec_status', '1')->where('rack_active', '1');
    }

    public function scopeByBusiness($query, $business)
    {
        return $query->where('rack_business', $business);
    }

    public function scopeByBranch($query, $branch)
    {
        return $query->where('rack_branch', $branch);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('rack_type', $type);
    }

    public function scopeRegular($query)
    {
        return $query->where('rack_type', '001');
    }

    public function scopeNED($query)
    {
        return $query->where('rack_type', '002');
    }

    public function scopeUnlocked($query)
    {
        return $query->where('rack_locked', '0');
    }

    // Accessors
    public function getRackTypeNameAttribute()
    {
        $types = [
            '001' => 'Regular',
            '002' => 'NED (Non-Expiry Date)',
        ];

        return $types[$this->rack_type] ?? 'Unknown';
    }

    public function getIsActiveAttribute()
    {
        return $this->rack_active === '1' && $this->rec_status === '1';
    }

    public function getIsLockedAttribute()
    {
        return $this->rack_locked === '1';
    }

    // Relationships
    public function gudang()
    {
        return $this->belongsTo(TguMsGudang::class, 'rack_branch', 'Gudang_code');
    }

    public function productBusiness()
    {
        return $this->belongsTo(TguMsProductBusiness::class, 'rack_business', 'Business');
    }

    // Helper methods
    public static function getRackTypes()
    {
        return [
            '001' => 'Regular',
            '002' => 'NED (Non-Expiry Date)',
        ];
    }

    public function getFullRackCodeAttribute()
    {
        return $this->rack_internal_code . ' - ' . $this->rack_branch;
    }

    // Override getKey for composite primary key support
    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[$key] = $this->getAttribute($key);
        }
        return $keys;
    }

    // Override setKeysForSaveQuery for composite primary key
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }
}