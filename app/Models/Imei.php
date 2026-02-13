<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imei extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'imeiID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imeis';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'invoice',
        'invoiceId',
        'phone',
        'phone_stock_code',
        'imei',
        'nonImei',
        'price',
        'allocatedTo',
        'number',
        'name',
        'activationDate',
        'DealSheetNr',
        'upgradeContract',
        'company',
        'entryAddedDate',
        'entryModifiedDate',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'activationDate' => 'date',
            'price' => 'decimal:2',
            'nonImei' => 'integer',
            'company' => 'integer',
            'entryAddedDate' => 'datetime',
            'entryModifiedDate' => 'datetime',
        ];
    }
}
