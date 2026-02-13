<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * List devices for the app (e.g. device picker when creating repair/order).
     * Optional: q (search product_code, bar_code, model), per_page.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 50), 100);
        $q = $request->input('q');

        $query = Device::query()
            ->with('manufacturer')
            ->orderBy('model');

        if ($q && is_string($q) && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $query->where(function ($query) use ($term) {
                $query->where('product_code', 'like', $term)
                    ->orWhere('bar_code', 'like', $term)
                    ->orWhere('model', 'like', $term);
            });
        }

        $devices = $query->paginate($perPage);

        return response()->json($devices);
    }
}
