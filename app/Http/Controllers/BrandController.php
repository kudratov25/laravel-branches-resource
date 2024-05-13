<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{

    public function index(Request $request)
    {
        return BrandResource::collection(Brand::latest()->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:brands',
                'domain' => 'required|unique:brands',
                'status' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $name = $request->name;
            $domain = $request->domain;
            $status = $request->status;
            $user_id = $request->user()->id;

            if ($request->file('image')) {
                // Save the uploaded image to the storage directory
                $imagePath = $request->file('image')->store('public/brand_images');
                $imagePath = str_replace('public/', '', $imagePath);
            } else {
                $imagePath = null;
            }

            $brand = new Brand();
            $brand->name = $name;
            $brand->domain = $domain;
            $brand->status = $status;
            $brand->user_id = $user_id;
            $brand->image_path = $imagePath; // Store the file path in the database
            $brand->save();
            $imageUrl = $imagePath ? url('storage/' . $imagePath) : null;
            $brand['image_path'] = $imageUrl;
            return response()->json(['message' => 'Brand created', 'brand' => $brand->toArray()], 200);
        } catch (ValidationException $e) {
            // Log the error
            Log::error('Validation error while creating brand: ' . $e->getMessage());
            // Return validation error response
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log other errors
            Log::error('Error occurred while creating brand: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {

        try {
            $request->validate([
                'name' => 'required|unique:brands,name,' . $brand->id,
                'domain' => 'required|unique:brands,domain,' . $brand->id,
                'status' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            $brand->name = $request->name;
            $brand->domain = $request->domain;
            $brand->status = $request->status;

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($brand->image_path) {
                    Storage::delete('public/' . $brand->image_path);
                }
                // Save the new image
                $imagePath = $request->file('image')->store('public/brand_images');
                $brand->image_path = str_replace('public/', '', $imagePath);
            }

            $brand->save();

            return response()->json(['message' => 'Brand updated', 'brand' => new BrandResource($brand)], 200);
        } catch (ValidationException $e) {
            // Log the error
            Log::error('Validation error while updating brand: ' . $e->getMessage());

            // Return validation error response
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log other errors
            Log::error('Error occurred while updating brand: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            // Delete the associated image from storage, if it exists
            if ($brand->image_path) {
                Storage::delete('public/' . $brand->image_path);
            }
            // Delete the brand instance from the database
            $brand->delete();

            return response()->json(['message' => 'Brand deleted'], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error occurred while deleting brand: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
