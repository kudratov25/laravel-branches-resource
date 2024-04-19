<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\District;
use App\Models\Region;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        if ($request->region) {
            $regionName = $request->region;
            $branches = Brand::whereHas('branches.district.region', function ($query) use ($regionName) {
                $query->where('name_uz', $regionName);
            })->with(['branches' => function ($query) use ($regionName) {
                $query->whereHas('district.region', function ($query) use ($regionName) {
                    $query->where('name_uz', $regionName);
                });
            }])->withCount('branches')->get();
            return response()->json(['brands' => $branches]);
        } else if ($request->district) {
            $branches = BranchResource::collection(District::where('name_uz', $request->district)->firstOrFail()->branches()->latest()->paginate(10)->withQueryString());
            return response()->json(["$request->district" . "dagi barcha filiallar soni " => count($branches), "$request->district" . "dagi barcha filiallar" => $branches]);
        } else if ($request->brand) {
            $branches = BranchResource::collection(Branch::whereHas('brand', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->brand . '%');
            })->latest()->paginate(10)->withQueryString());
            return response()->json(["$request->brand" . "ning barcha filiallari soni" => count($branches), "$request->brand" . "ning filiallari jumladan" => $branches]);
        }

        return response()->json(['Message' => "Search not found"], 404);
    }
}
