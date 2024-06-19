<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CostcoScraper;
use App\Models\Warning;

class HomeController extends Controller
{
    public function dahsboardIndex() {
        return view("dashboard");
    }

    public function storeProduct(Request $request) {
        if ($request->site == 1)
            return CostcoScraper::insertProduct($request);
    }

    public function checkWarnings(Request $request)
    {
        $lastChecked = $request->query('lastChecked');

        $warnings = Warning::with("product")->where('created_at', '>', $lastChecked)->get();

        return response()->json($warnings);
    }
    public function removeWarning(Request $request)
    {
        $id = $request->query('id');

        $warning = Warning::find($id);
        $warning->delete();

        return response()->json([
            "status" => true
        ]);
    }
    public function destroyAll()
    {
        Warning::truncate();

        return response()->json([
            "status" => true
        ]);
    }
}
