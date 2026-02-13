<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * List customers for repair creation (e.g. dropdown/picker).
     * Optional: q (search name/surname/company), per_page.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 50), 100);
        $q = $request->input('q');

        $query = Customer::query()
            ->orderBy('surname')
            ->orderBy('name');

        if ($q && is_string($q) && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('surname', 'like', $term)
                    ->orWhere('company_name', 'like', $term);
            });
        }

        $customers = $query->paginate($perPage);

        return response()->json($customers);
    }

    /**
     * Show a single customer. Optionally include repairs via ?with=repairs.
     */
    public function show(Request $request, Customer $customer): JsonResponse
    {
        if ($request->boolean('with_repairs') || $request->input('with') === 'repairs') {
            $customer->load(['repairs' => fn ($q) => $q->orderByDesc('created_at')]);
        }

        return response()->json($customer);
    }
}
