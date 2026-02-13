<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'repair_form_terms',
        'repair_invoice_terms',
    ];

    /**
     * Get the singleton repair settings row (creates with defaults if missing).
     */
    public static function get(): self
    {
        $row = self::first();
        if ($row) {
            return $row;
        }
        return self::create([
            'repair_form_terms' => '',
            'repair_invoice_terms' => '',
        ]);
    }
}
