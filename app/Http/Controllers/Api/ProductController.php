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
        try {

            $category = $request->get('category');
            $limit = $request->get('limit');
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
                $productsQuery->where('name', 'like', "%{$search}%");
            }

            // Filter by best sellers
            if ($bestSeller) {
                $productsQuery->addSelect([
                    'sales_count' => DB::table('transaction_details')
                        ->selectRaw('SUM(qty)')
                        ->whereColumn('product_id', 'products.id')
                        ->groupBy('product_id'),
                ])
                    ->orderByDesc('sales_count');
            }

            // Paginate results
            $products = $productsQuery->paginate($limit);

            return response()->json([
                'message' => 'Products retrieved successfully.',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving products.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function detail($id)
    {
        try {
            $product = Product::with('catalogImages')
                ->where('is_active', true)
                ->select([
                    'products.*',
                    DB::raw('(
            SELECT SUM(qty) 
            FROM transaction_details 
            WHERE transaction_details.product_id = products.id
        ) as sales_count')
                ])
                ->findOrFail($id);
            return response()->json([
                'message' => 'Product details retrieved successfully.',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving products.',
                'error' => $e->getMessage()
            ]);
        }
    }
}