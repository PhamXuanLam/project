<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function create(ProductRequest $request) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin" && $account->role != "seller") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        $response = [];

        DB::beginTransaction();
        try {
            $product = new Product();
            // Gán các giá trị từ request, nhưng không gán product_id (sẽ lấy từ request)
            $product->product_id = $request->product_id;
            $product->seller_id = $request->seller_id;
            $product->product_name = $request->product_name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->min_price = $request->min_price;
            $product->max_price = $request->max_price;
            $product->save(); // Lưu sản phẩm

            // Sau khi lưu, product_id đã có giá trị thực tế
            $variant = new Variant();
            $variant->variant_id = $request->variant_id; // Gán variant_id từ request
            $variant->variant_name = $request->variant_name;
            $variant->product_id = $product->product_id; // Gán product_id đã lưu
            $variant->color = $request->color;
            $variant->size = $request->size;
            $variant->material = $request->material;
            $variant->price = $request->price;
            $variant->stock_quantity = $request->stock_quantity;
            $variant->save(); // Lưu biến thể

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Register successfully!";
            $response["data"] = [
                "product" => $product, // Trả về đối tượng product với đầy đủ thông tin
                "variant" => $variant, // Trả về đối tượng variant với đầy đủ thông tin
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Register failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function update(ProductRequest $request, string $product_id) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "seller") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        $response = [];

        DB::beginTransaction();
        try {
            $product = Product::find($product_id);
            // Gán các giá trị từ request, nhưng không gán product_id (sẽ lấy từ request)
            $product->product_name = $request->product_name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->min_price = $request->min_price;
            $product->max_price = $request->max_price;
            $product->save(); // Lưu sản phẩm

            // Sau khi lưu, product_id đã có giá trị thực tế
            $variant = Variant::find($request->variant_id);
            $variant->variant_id = $request->variant_id; // Gán variant_id từ request
            $variant->variant_name = $request->variant_name;
            $variant->product_id = $product->product_id; // Gán product_id đã lưu
            $variant->color = $request->color;
            $variant->size = $request->size;
            $variant->material = $request->material;
            $variant->price = $request->price;
            $variant->stock_quantity = $request->stock_quantity;
            $variant->save(); // Lưu biến thể

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Update successfully!";
            $response["data"] = [
                "product" => $product, // Trả về đối tượng product với đầy đủ thông tin
                "variant" => $variant, // Trả về đối tượng variant với đầy đủ thông tin
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Update failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function productsInactive() {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        // Lấy danh sách sản phẩm có is_approved = false và không bao gồm created_at, updated_at
        $products = Product::where('is_approved', false)
                    ->with(['variants' => function($query) {
                        $query->select('variant_id', 'variant_name', 'product_id', 'color', 'size', 'material', 'price', 'stock_quantity', 'is_active');
                    }])
                    ->get(['product_id', 'seller_id', 'product_name', 'description', 'category_id', 'min_price', 'max_price', 'is_approved']);

        // Trả về JSON
        return response()->json([
            'status' => true,
            'message' => 'Unapproved products with variants retrieved successfully',
            'data' => $products
        ]);

    }

    public function active(string $product_id) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        DB::beginTransaction();
        try {
            $product = Product::find($product_id);
            $product->is_approved = true;
            $product->save();

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Đã kích hoạt sản phẩm!";

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Có lỗi sảy ra!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function delete(string $product_id) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "seller") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        DB::beginTransaction();
        try {
            $product = Product::find($product_id);
            $product->delete();

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Đã xóa sản phẩm khỏi gian hàng!";

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Có lỗi sảy ra!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function productsActive() {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        // Lấy danh sách sản phẩm có is_approved = false và không bao gồm created_at, updated_at
        $products = Product::where('is_approved', true)
                    ->with(['variants' => function($query) {
                        $query->select('variant_id', 'variant_name', 'product_id', 'color', 'size', 'material', 'price', 'stock_quantity', 'is_active');
                    }])
                    ->get(['product_id', 'seller_id', 'product_name', 'description', 'category_id', 'min_price', 'max_price', 'is_approved']);

        // Trả về JSON
        return response()->json([
            'status' => true,
            'message' => 'Approved products with variants retrieved successfully',
            'data' => $products
        ]);

    }
}
