<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'delivery_id' => 'required|uuid|exists:deliveries,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|uuid|exists:products,id',
            'products.*.qty' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }
        DB::beginTransaction();

        try {
            $transactionCode = Transaction::generateTransactionCode();

            $transaction = Transaction::create([
                'id' => Str::uuid(),
                'transaction_code' => $transactionCode,
                'customer' => $request->customer,
                'address' => $request->address,
                'phone' => $request->phone,
                'delivery_id' => $request->delivery_id,
                'delivery_fee' => null,
                'status' => 'process',
            ]);
            foreach ($request->products as $detail) {
                $product = Product::find($detail['product_id']);

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'details' => "Product {$product->name} is not active.",
                    ]);
                }

                $detailAmount = $product->price * $detail['qty'];

                TransactionDetail::create([
                    'id' => Str::uuid(),
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $detail['qty'],
                    'amount' => $detailAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaction created successfully.',
                'data' => $transaction->load('TransactionDetails'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}