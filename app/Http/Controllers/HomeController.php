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
        $search = $request->input('search', '');

        $productsQuery = Product::query();

        if ($sort == 'stock_level_asc') {
            $productsQuery->orderBy('stock_level', 'asc');
        } elseif ($sort == 'stock_level_desc') {
            $productsQuery->orderBy('stock_level', 'desc');
        } elseif ($search)
            $productsQuery
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('code', 'like', '%' . $search . '%')
            ->orWhere('url', 'like', '%' . $search . '%');
        else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(20)->appends(['sort' => $sort, "search" => $search]);

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

    public function deletSelected(Request $request) {
        $products = $request->input("products", []);
        foreach ($products as $id) {
            $product = Product::find($id);
            $product->delete();
        }

        return redirect()->back()
        ->with('success', 'Products deleted successfuly');
    }
}
