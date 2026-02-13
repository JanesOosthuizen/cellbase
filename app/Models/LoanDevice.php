<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'imei',
    ];

    /**
     * Get the device (phone) this loan device is linked to.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the repair this loan device is allocated to (if any).
     */
    public function repair(): HasOne
    {
        return $this->hasOne(Repair::class);
    }
}
