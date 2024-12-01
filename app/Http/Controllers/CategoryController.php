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

        $categories = Category::with('children')
        ->select('category_id', 'name', 'description', 'parent_id', 'image', 'expired')
        ->get();
        // Đệ quy để xây dựng cấu trúc cây
        $structuredCategories = $this->buildCategoryTree($categories);

        // Trả về JSON hoặc view
        return response()->json($structuredCategories);
    }

    private function buildCategoryTree($categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $children = $this->buildCategoryTree($categories, $category->category_id);
                if ($children) {
                    $category->children = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }

    public function active(string $category_id) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => false,
                "message" => "You do not have permission to create new categories!"
            ]);
        }
        $category = Category::find($category_id);
        DB::beginTransaction();
        try {
            $parentExpired = $category->expired;
            $category->expired = null;
            $category->save();
            foreach($category->children as $child) {
                if ($child->expired == $parentExpired) {
                    $child->expired = null;
                    $child->save();
                }
            }
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Activate categories and related categories!";
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("File: ".$e->getFile().'---Line: '.$e->getLine()."---Message: ".$e->getMessage());
            $response["status"] = false;
            $response["message"] = "An error occurred.!";
            $response["error"] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function search(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ tham số 'keyword'
        $keyword = $request->input('keyword');

        // Tìm kiếm danh mục theo 'name' hoặc 'id'
        $categories = Category::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('category_id', 'LIKE', '%' . $keyword . '%')
            ->with('parent') // Gọi đến quan hệ 'parent' để lấy tên danh mục cha
            ->get();

        // Định dạng dữ liệu trả về cho từng danh mục
        $result = $categories->map(function ($category) {
            return [
                'category_id' => $category->category_id,
                'name' => $category->name,
                'image_url' => $category->image_url,
                'description' => $category->description,
                'parent' => $category->parent ? $category->parent->name : null
            ];
        });

        return response()->json($result);
    }

    public function searchName(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ tham số 'keyword'
        $keyword = $request->input('keyword');

        // Tìm kiếm danh mục theo 'name' hoặc 'category_id'
        $categories = Category::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('category_id', 'LIKE', '%' . $keyword . '%')
            ->get(['category_id', 'name']); // Lấy ra chỉ 'category_id' và 'name'

        // Định dạng dữ liệu trả về cho từng danh mục
        $result = $categories->map(function ($category) {
            return [
                'category_id' => $category->category_id,
                'name' => $category->name,
            ];
        });

        return response()->json($result);
    }

}
