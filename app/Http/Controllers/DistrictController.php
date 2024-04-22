<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\District;
use App\Models\Region;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {

        if ($request->region) {
            $request->validate([
                'region' => 'required|string',
            ]);
            $regionName = $request->input('region');
            $region = Region::where('name_uz', $regionName)->firstOrFail();
            $branchCounts = [];
            foreach ($region->district as $district) {
                $branches = Branch::where('district_id', $district->id)->get();
                $branchCountsInDistrict = [];
                foreach ($branches as $branch) {
                    $brandName = $branch->brand->name; // Assuming 'name' is the column for brand name
                    if (!isset($branchCountsInDistrict[$brandName])) {
                        $branchCountsInDistrict[$brandName] = 1;
                    } else {
                        $branchCountsInDistrict[$brandName]++;
                    }
                }
                if (!empty($branchCountsInDistrict)) {
                    $branchCounts[$district->name_uz] = $branchCountsInDistrict;
                }
            }

            return response()->json(['branchs in ' . $regionName => $branchCounts]);
        }

        else if ($request->district) {
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
