<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceArchive extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'devices_archive';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_code',
        'bar_code',
        'manufacturer_id',
        'model',
        'cost_excl',
        'cost_incl',
        'rsp_excl',
        'rsp_incl',
        'batch_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_excl' => 'decimal:2',
            'cost_incl' => 'decimal:2',
            'rsp_excl' => 'decimal:2',
            'rsp_incl' => 'decimal:2',
        ];
    }

    /**
     * Get the manufacturer that owns the device.
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
