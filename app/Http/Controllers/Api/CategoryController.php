<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories with pagination
        // Get paginated categories with status equal to 1
        $categories = Category::where('status', 1)->paginate(10); // Change 10 to the desired number of items per page

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Getting categories successful.',
                'categories' => $categories
            ], 200);
        } else {
            // If the request is not JSON, return view with paginated categories
            return view('categories.index', ['categories' => $categories]);
        }
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories',
            'sku' => 'nullabe|string|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules for image
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $data = $validator->validated();

        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded file
            $newimage = $request->file('image');
            $imageName = Helper::imageUpload($newimage, 'images/category');

            // Add image name to data array
            $data['image'] = $imageName;
        }
        $sku = strtolower(str_replace(' ', '-', $data['name']));
        // Create the category
        $category = Category::create([
            'name' => $data['name'],
            'sku' => $sku,
            'image' => $data['image'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully.',
            'category' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category found',
            'category' => $category
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }
        // return $request->all();
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $id,
            'sku' => 'nullable|string|unique:categories,sku,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validator Error',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();

        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded file
            $newimage = $request->file('image');
            $imageName = Helper::ImageUploadAndDelete($newimage, $category->image, 'images/category');

            // Add image name to data array
            $data['image'] = $imageName;
        }

        // Generate SKU from the category name (e.g., remove spaces and convert to lowercase)
        $sku = strtolower(str_replace(' ', '-', $data['name']));

        // Update the category attributes
        $category->update(array_merge($data, ['sku' => $sku]));

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully.',
            'category' => $category
        ], 200);
    }

    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Delete the category
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.'
        ], 200);
    }

    public function dataForDataTable(Request $request)
    {
        // Get search keyword from the request
        $search = $request->input('search');

        // Query to fetch categories with status equal to 1
        $query = Category::where('status', 1);

        // Apply search filter if search keyword is provided
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
            });
        }

        // Fetch all records matching the query
        $categories = $query->get(['id', 'name', 'sku', 'image']);

        return response()->json([
            'draw' => 1, // Since there is no pagination, draw can be any value
            'recordsTotal' => $categories->count(),
            'recordsFiltered' => $categories->count(),
            'data' => $categories,
        ]);
    }
}
