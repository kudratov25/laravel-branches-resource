<?php

namespace App\Http\Controllers;

use App\Events\AttachmentEvent;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\District;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
// use App\Exceptions\ValidationFailedException;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected AttachmentService $attachmentService)
    {
    }

    public function index(Request $request)
    {

        // search function based on districts
        if ($request->district) {
            return BranchResource::collection(District::where('name_uz', $request->district)->firstOrFail()->branches()->latest()->paginate(10)->withQueryString());
        }
        // search based on brand names
        else if ($request->brand) {
            return BranchResource::collection(
                Branch::whereHas('brand', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->brand . '%');
                })
                    ->latest()
                    ->paginate(10)
                    ->withQueryString()
            );
        }

        return BranchResource::collection(Branch::latest()->paginate(10));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'brand_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $name = $request->name;
        $brand_id = $request->brand_id;
        $district_id = $request->district_id;

        $branch = new Branch();
        $branch->name = $name;
        $branch->brand_id = $brand_id;
        $branch->district_id = $district_id;
        $branch->save();

        event(new AttachmentEvent($request->images, $branch->images()));
        return response()->json(['success' => $branch], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return new BranchResource($branch->load('images'));
    }



    public function update(Request $request, Branch $branch)
    {
        try {
            $request->validate([
                'name' => 'required',
                'brand_id' => 'required|numeric',
                'district_id' => 'required|numeric',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $branch->name = $request->name;
            $branch->brand_id = $request->brand_id;
            $branch->district_id = $request->district_id;

            if ($request->images) {
                $oldImages = $branch->images;
                $this->attachmentService->destroy($oldImages);
                $branch->images()->delete();
                event(new AttachmentEvent($request->images, $branch->images()));
            }
            $branch->save();

            return response()->json(['success' => new BranchResource($branch)], 200);
        } catch (ValidationException $e) {
            // Log the error
            Log::error('Validation error while updating brand: ' . $e->getMessage());
            // Return validation error response
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'Failed to update branch.'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            $branch->images()->delete();
            $branch->delete();
            return response()->json(['message' => 'branch deleted'], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error occurred while deleting branch: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
