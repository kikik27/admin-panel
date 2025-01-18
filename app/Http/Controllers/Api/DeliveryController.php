<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->query('search');
        $deliveries = Delivery::query()
            ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
            })
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->paginate(10);

        return response()->json([
            'message' => 'Deliveries retrieved successfully.',
            'data' => $deliveries,
        ]);
    }
}
