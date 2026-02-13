<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Imei;
use App\Models\Repair;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    private const LIMIT = 6;

    /**
     * Global search across IMEIs, customers, and repairs. Returns JSON for nav dropdown.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = $request->input('q');
        if (! is_string($q) || trim($q) === '') {
            return response()->json([
                'imeis' => [],
                'customers' => [],
                'repairs' => [],
            ]);
        }

        $term = '%' . trim($q) . '%';

        $imeis = Imei::query()
            ->where(function ($query) use ($term) {
                $query->where('imei', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('name', 'like', $term)
                    ->orWhere('number', 'like', $term);
            })
            ->orderByDesc('imeiID')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Imei $imei) => [
                'id' => $imei->imeiID,
                'label' => $imei->imei ?: ($imei->phone ?? '#' . $imei->imeiID),
                'sub' => $imei->name ?? $imei->phone,
                'url' => route('imeis.index', ['q' => $q]),
            ]);

        $customers = Customer::query()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('surname', 'like', $term)
                    ->orWhere('company_name', 'like', $term);
            })
            ->orderBy('surname')
            ->orderBy('name')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'label' => trim($c->surname . ', ' . $c->name) ?: ($c->company_name ?? 'Customer #' . $c->id),
                'sub' => $c->company_name ?: $c->email,
                'url' => route('customers.show', $c),
            ]);

        $repairs = Repair::query()
            ->with('customer')
            ->where(function ($query) use ($term, $q) {
                $query->where('imei', 'like', $term)
                    ->orWhere('fault_description', 'like', $term)
                    ->orWhereHas('customer', function ($query) use ($term) {
                        $query->where('name', 'like', $term)
                            ->orWhere('surname', 'like', $term)
                            ->orWhere('company_name', 'like', $term);
                    });
                if (is_numeric(trim($q))) {
                    $query->orWhere('id', '=', (int) trim($q));
                }
            })
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Repair $r) => [
                'id' => $r->id,
                'label' => 'Repair #' . $r->id,
                'sub' => $r->customer ? trim($r->customer->name . ' ' . $r->customer->surname) : null,
                'status' => $r->status_label,
                'url' => route('repairs.show', $r),
            ]);

        return response()->json([
            'imeis' => $imeis->values()->all(),
            'customers' => $customers->values()->all(),
            'repairs' => $repairs->values()->all(),
        ]);
    }
}
