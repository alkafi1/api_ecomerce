<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        return $subcategories = SubCategories::with('category')->paginate(10);

        return view('subcategories.index', ['subcategories' => $subcategories]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:sub_categories',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Generate SKU from the name
        $sku = Str::snake($request->input('name'));

        // Create the SubCategory
        SubCategories::create([
            'name' => $request->input('name'),
            'sku' => $sku,
            'category_id' => $request->input('category_id'),
        ]);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory created successfully.');
    }

    public function show($id)
    {
        $subCategory = SubCategories::find($id);

        if (!$subCategory) {
            return redirect()->back()->with('error', 'SubCategory not found.');
        }

        return view('subcategories.show', ['subCategory' => $subCategory]);
    }

    public function update(Request $request, $id)
    {
        // Find the SubCategory by ID
        $subCategory = SubCategories::find($id);

        if (!$subCategory) {
            return redirect()->back()->with('error', 'SubCategory not found.');
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Generate SKU from the updated name
        $sku = Str::snake($request->input('name'));

        // Update the SubCategory attributes
        $subCategory->update([
            'name' => $request->input('name'),
            'sku' => $sku,
            'category_id' => $request->input('category_id'),
        ]);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory updated successfully.');
    }

    public function destroy($id)
    {
        // Find the SubCategory by ID
        $subCategory = SubCategories::find($id);

        if (!$subCategory) {
            return redirect()->back()->with('error', 'SubCategory not found.');
        }

        // Delete the SubCategory
        $subCategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'SubCategory deleted successfully.');
    }
}
