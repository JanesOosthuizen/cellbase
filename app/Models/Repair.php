<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends Model
{
    use HasFactory;

    public const STATUS_BOOKED_IN = 'booked_in';
    public const STATUS_SENT_AWAY = 'sent_away';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_COLLECTED = 'collected';

    public const STATUS_LABELS = [
        self::STATUS_BOOKED_IN => 'Booked In',
        self::STATUS_SENT_AWAY => 'Sent Away',
        self::STATUS_RECEIVED => 'Received',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_COLLECTED => 'Collected',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'phone',
        'imei',
        'cell_nr',
        'contact_nr',
        'allocated_to',
        'loan_device_id',
        'fault_description',
        'ticket_status',
    ];

    /**
     * Accessors to append to JSON.
     *
     * @var list<string>
     */
    protected $appends = ['status_label'];

    /**
     * Get the customer that owns the repair.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the external user (supplier) allocated to this repair.
     */
    public function allocatedTo(): BelongsTo
    {
        return $this->belongsTo(ExternalUser::class, 'allocated_to');
    }

    /**
     * Get the loan device allocated to the customer for this repair.
     */
    public function loanDevice(): BelongsTo
    {
        return $this->belongsTo(LoanDevice::class);
    }

    /**
     * Get the events for the repair.
     */
    public function events(): HasMany
    {
        return $this->hasMany(RepairEvent::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->ticket_status] ?? $this->ticket_status;
    }
}
