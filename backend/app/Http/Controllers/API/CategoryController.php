<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        // Get all categories
        $categories = Category::all();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        // Create new category
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);  // Auto creating slug
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);  // Fixed: should use $request->name, not $category->name
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(Request $request, Category $category)
    {
        $this->authorize('delete', $category);

        // Delete the category
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
