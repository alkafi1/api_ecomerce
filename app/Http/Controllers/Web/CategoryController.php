<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories with pagination
        $categories = Category::where('status', 1)->paginate(10);

        return view('categories.index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories',
            'sku' => 'nullable|string|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded file
            $image = $request->file('image');

            // Store the image
            $imageName = $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);

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

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        return view('categories.show', ['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('categories')->ignore($id),
            ],
            'sku' => [
                'nullable',
                'string',
                Rule::unique('categories')->ignore($id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Generate SKU from the category name
        $sku = strtolower(str_replace(' ', '-', $data['name']));

        // Update the category attributes
        $category->update(array_merge($data, ['sku' => $sku]));

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        // Delete the category
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
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

        return view('categories.index', compact('categories'));
    }
}
