<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Imei;
use Illuminate\Http\Request;

class ImeiController extends Controller
{
    /**
     * Display all IMEIs.
     */
    public function index()
    {
        $imeis = Imei::orderBy('created_at', 'desc')
            ->paginate(50);

        return view('imeis.index', compact('imeis'));
    }

    /**
     * Check if an IMEI exists in the database.
     */
    public function checkImei(Request $request)
    {
        $imei = $request->input('imei');
        
        if (empty($imei)) {
            return response()->json([
                'exists' => false,
                'message' => '',
            ]);
        }

        $exists = Imei::where('imei', $imei)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'IMEI already exists in the system' : 'IMEI is available',
        ]);
    }
}
