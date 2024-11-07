<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function update(CategoryRequest $request, string $category_id) {

        $account = Auth::guard('account_api')->user();
        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }
        $response = [];

        $params = $request->only([
            'name', 'parent_id', 'image', "description", "category_id"
        ]);

        $category = Category::find($category_id);

        if($category == null) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        DB::beginTransaction();
        try {
            $category->fill($params);
            $category->save();
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Update category successfully!";
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Update category failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function delete(string $category_id) {

        $account = Auth::guard('account_api')->user();
        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }
        $response = [];

        $category = Category::find($category_id);

        if($category == null) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        DB::beginTransaction();
        try {
            $category->expired = now();
            $category->save();
            foreach($category->children as $child) {
                $child->expired = now();
                $child->save();
            }
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Disabled categories and related categories!";
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "An error occurred.!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function create(CategoryRequest $request) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        $response = [];

        $params = $request->only([
            'name', 'parent_id', 'image', "description", "category_id"
        ]);

        DB::beginTransaction();
        $category = new Category();
        try {
            $category->fill($params);
            $category->save();
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Create category successfully!";
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "Create category failure!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function index() {
        DB::beginTransaction();
        try {
            Category::whereNotNull('expired')
            ->where('expired', '<', now()->subDays(30))
            ->delete(); // Xóa trực tiếp trong cơ sở dữ liệu
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        $categories = Category::select('category_id', 'name', 'description', 'parent_id', 'image', "expired")
                ->with('parent') // Lấy danh mục cha thông qua quan hệ
                ->get();

        $categories = $categories->map(function ($category) {
            $category->parent_name = $category->parent ? $category->parent->name : null;
            unset($category->parent_id); // Bỏ trường parent_id nếu không cần thiết
            unset($category->parent); // Bỏ quan hệ parent nếu không cần thiết

            return $category;
        });

        // Trả về JSON (hoặc sử dụng trong view tuỳ ý)
        return response()->json($categories);

    }
}
