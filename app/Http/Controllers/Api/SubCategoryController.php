<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $data = $validator->validated();

        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded file
            $newimage = $request->file('image');
            $imageName = Helper::imageUpload($newimage, 'images/subcategory');

            // Add image name to data array
            $data['image'] = $imageName;
        }
        // Generate SKU from the name
        $sku = strtolower(str_replace(' ', '-', $request->input('name')));
        

        // Create the SubCategory
        $subCategory = SubCategories::create([
            'name' => $data['name'],
            'sku' => $sku,
            'image' => $data['image'] ?? null,
            'category_id' => $request->input('category_id'),
        ]);
        $subCategory->load('category');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded file
            $newimage = $request->file('image');
            $imageName = Helper::ImageUploadAndDelete($newimage, $subCategory->image, 'images/subcategory');

            // Add image name to data array
            $data['image'] = $imageName;
        }

        // Generate SKU from the category name (e.g., remove spaces and convert to lowercase)
        $sku = strtolower(str_replace(' ', '-', $request->input('name')));

        // Update the SubCategory attributes
        $subCategory->update(array_merge($data, ['sku' => $sku]));
        $subCategory->load('category');
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