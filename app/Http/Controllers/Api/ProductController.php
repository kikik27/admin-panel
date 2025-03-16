<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $limit = $request->has('limit');
        $search = $request->query('name');
        $bestSeller = $request->query('best_seller');

        $productsQuery = Product::query()
            ->with('catalogImages') // Load related catalog images
            ->where('is_active', true); // Only active products

        // Filter by category
        if ($category) {
            $productsQuery->where('category', $category);
        }

        // Search by name
        if ($search) {
            $productsQuery->where('name', 'like', '%' . $search . '%');
        }

        // Filter by best sellers
        if ($bestSeller) {
            $productsQuery->addSelect([
                'sales_count' => DB::table('transaction_details')
                    ->selectRaw('SUM(qty)')
                    ->whereColumn('products_id', 'products.id')
                    ->groupBy('products_id'),
            ])
                ->orderByDesc('sales_count');
        }

        // Paginate results
        $products = $productsQuery->paginate($limit || 10);

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products,
        ]);
    }
}