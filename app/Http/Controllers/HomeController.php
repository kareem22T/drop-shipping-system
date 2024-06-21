<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CostcoScraper;
use App\Models\Warning;
use App\Models\Product;

class HomeController extends Controller
{
    public function dahsboardIndex(Request $request) {
        $sort = $request->input('sort', 'latest');

        $productsQuery = Product::query();

        if ($sort == 'stock_level_asc') {
            $productsQuery->orderBy('stock_level', 'asc');
        } elseif ($sort == 'stock_level_desc') {
            $productsQuery->orderBy('stock_level', 'desc');
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(20)->appends(['sort' => $sort]);

        return view('dashboard', compact('products', 'sort'));
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
    public function removeProduct(Request $request) {
        $product = Product::find($request->id);
        $product->delete();

        return redirect()->back()
        ->with('success', 'Product deleted successfuly');
    }
}
