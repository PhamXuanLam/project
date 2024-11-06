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
    public function create(CategoryRequest $request) {
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin") {
            return response()->json([
                "status" => true,
                "message" => "You do not have permission to create new categories!"
            ]);
        }

        $response = [];

        $params = $request->only([
            'name', 'parent_id', 'image', "description"
        ]);

        DB::beginTransaction();
        $category = new Category();
        try {
            $category->fill($params);
            $category->save();
            DB::commit();
            $response["status"] = true;
            $response["message"] = "Create category successfully!";
            $response["info"] = $category;
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
        $account = Auth::guard('account_api')->user();

        if($account->role != "admin" && $account->role != "seller") {
            return response()->json([
                "status" => true,
                "message" => "You do not have permission to view the category list!"
            ]);
        }

        $categories = Category::select('id', 'name', 'parent_id')->get();

        $categoryTree = $this->buildCategoryTree($categories);

        // Trả về JSON (hoặc sử dụng trong view tuỳ ý)
        return response()->json($categoryTree);


    }

    function buildCategoryTree($categories, $parentId = null)
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                // Tìm các danh mục con của danh mục hiện tại
                $children = $this->buildCategoryTree($categories, $category->id);

                // Nếu có danh mục con, gắn nó vào danh mục hiện tại
                if ($children) {
                    $category->children = $children;
                }

                $tree[] = $category;
            }
        }

        return $tree;
    }

}
