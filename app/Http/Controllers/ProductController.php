<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Account;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\ProductNotificationEvent;
use App\Models\Notification;

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
            if($request->variant_id) {
                $variant->variant_id = $request->variant_id; // Gán variant_id từ request
                $variant->variant_name = $request->variant_name;
                $variant->product_id = $product->product_id; // Gán product_id đã lưu
                $variant->color = $request->color;
                $variant->size = $request->size;
                $variant->material = $request->material;
                $variant->price = $request->price;
                $variant->stock_quantity = $request->stock_quantity;
                $variant->save(); // Lưu biến thể
            }

            $admins = Account::where('role', 'admin')->get();

            $notifications = [];
            foreach ($admins as $admin) {
                $message = "Người bán '{$account->name}' muốn đăng bán sản phẩm '{$product->product_name}'.";

                // Lưu thông báo vào cơ sở dữ liệu
                $notifications[] = [
                    'account_id' => $admin->id,
                    'type' => 'product',
                    'message' => $message,
                    'is_read' => false,
                    'created_at' => now(),
                ];

                // Phát sự kiện real-time qua Pusher
                broadcast(new ProductNotificationEvent($message));
            }

            Notification::insert($notifications);

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

    public function status(string $product_id) {
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
            $product->is_approved = !$product->is_approved;
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

    public function show(string $product_id) {
        $product = Product::find($product_id);
        if($product == null) {
            return response()-> json([
                "status" => false,
                "message" => "sản phẩm không tồn tại"
            ]);
        }
        return response()->json([
            "status" => true,
            "info" => $product
        ]);
    }

    public function searchByShop(string $keyword) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword is required.',
            ], 400);
        }

        $groupedProducts = User::where('unit_name', 'LIKE', "%$keyword%")
        ->where('position', 'seller') // Chỉ lấy những user là seller
        ->with(['products'])
        ->get(['id', 'unit_name']) // Chỉ lấy id và unit_name của cửa hàng
        ->map(function ($user) {
            return [
                'unit_name' => $user->unit_name,
                'products' => $user->products,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $groupedProducts,
        ]);
    }

    public function searchProductsOfShop(Request $request) {
        $account = Auth::guard('account_api')->user();
        if (!$account || $account->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $user = $account->user; // Quan hệ `Account` -> `User`
        if (!$user) {
            return response()->json(['message' => 'User not found for this account'], 404);
        }
        $status = $request->status;
        $keyword = $request->keyword;

        // Lấy danh sách sản phẩm theo điều kiện
        $products = Product::where('seller_id', $user->id) // seller_id = id ở bảng users
            ->where('is_approved', $status)
            ->when($keyword, function ($query, $keyword) {
                $query->where('product_name', 'like', "%$keyword%");
            })
            ->get();

        return response()->json($products);
    }

    public function search(Request $request) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $products = Product::query();
        if ($keyword) {
            $products->where('product_name', 'LIKE', "%$keyword%")
                     ->orWhere('description', 'LIKE', "%$keyword%");
        }
        if($status) {
            $products->where('is_approved', $status);
        }
        return response()->json([
            'success' => true,
            'data' => $products->get(),
        ]);
    }
}
