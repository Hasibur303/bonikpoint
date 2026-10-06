<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductVisitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductLikeController extends Controller
{
    public function update(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);
        $data = $request->validate(['liked' => ['required', 'boolean']]);
        $hash = ProductVisitor::hash($request, true);

        return DB::transaction(function () use ($product, $data, $hash) {
            Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $likes = DB::table('product_likes')->where('product_id', $product->id);

            if ($data['liked']) {
                DB::table('product_likes')->insertOrIgnore([
                    'product_id' => $product->id,
                    'visitor_hash' => $hash,
                    'created_at' => now(),
                ]);
            } else {
                (clone $likes)->where('visitor_hash', $hash)->delete();
            }

            return response()->json([
                'liked' => (bool) $data['liked'],
                'likes_count' => $likes->count(),
            ]);
        });
    }
}
