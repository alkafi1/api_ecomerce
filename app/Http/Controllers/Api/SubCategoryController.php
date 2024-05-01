<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories = SubCategories::with('category')->paginate(10);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Getting subcategories successful.',
                'subcategories' => $subcategories
            ], 200);
        } else {
            // If the request is not JSON, return view with paginated subcategories
            return view('subcategories.index', ['subcategories' => $subcategories]);
        }
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:sub_categories',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Generate SKU from the name
        $sku = Str::snake($request->input('name'));

        // Create the SubCategory
        $subCategory = SubCategories::create([
            'name' => $request->input('name'),
            'sku' => $sku,
            'category_id' => $request->input('category_id'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'SubCategory created successfully.',
            'subCategory' => $subCategory
        ], 201);
    }

    public function show($id)
    {
        $SubCategories = SubCategories::find($id);

        if (!$SubCategories) {
            return response()->json([
                'status' => false,
                'message' => 'SubCategories not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'SubCategories found',
            'SubCategories' => $SubCategories
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Find the SubCategory by ID
        $subCategory = SubCategories::find($id);

        if (!$subCategory) {
            return response()->json([
                'status' => false,
                'message' => 'SubCategory not found'
            ], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('sub_categories')->ignore($id),
            ],
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate SKU from the updated name
        $sku = Str::snake($request->input('name'));

        // Update the SubCategory attributes
        $subCategory->update([
            'name' => $request->input('name'),
            'sku' => $sku,
            'category_id' => $request->input('category_id'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'SubCategory updated successfully.',
            'subCategory' => $subCategory
        ], 200);
    }

    public function destroy($id)
    {
        // Find the SubCategories by ID
        $SubCategories = SubCategories::find($id);

        if (!$SubCategories) {
            return response()->json([
                'status' => false,
                'message' => 'SubCategories not found'
            ], 404);
        }
        // Delete the SubCategories
        $SubCategories->delete();

        return response()->json([
            'status' => true,
            'message' => 'SubCategories deleted successfully.'
        ], 200);
    }
}
