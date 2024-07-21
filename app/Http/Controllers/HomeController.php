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
        $productsQuery->where('site', 1);

        if ($sort == 'stock_level_asc') {
            $productsQuery->orderBy('stock_level', 'asc');
        } elseif ($sort == 'stock_level_desc') {
            $productsQuery->orderBy('stock_level', 'desc');
        }else if ($sort == 'avaliable_value_asc') {
            $productsQuery->orderBy('existance', 'asc');
        } elseif ($sort == 'avaliable_value_desc') {
            $productsQuery->orderBy('existance', 'desc');
        } elseif ($sort == 'discount_exp_asc') {
            $productsQuery->orderByRaw('STR_TO_DATE(discount_exp, "%Y-%m-%d") asc');
        } elseif ($sort == 'discount_exp_desc') {
            $productsQuery->orderByRaw('STR_TO_DATE(discount_exp, "%Y-%m-%d") desc');
        } elseif ($sort == 'discount_value_asc') {
            $productsQuery->orderByRaw('CAST(discount_value AS DECIMAL(10,2)) asc');
        } elseif ($sort == 'discount_value_desc') {
            $productsQuery->orderByRaw('CAST(discount_value AS DECIMAL(10,2)) desc');
        } elseif ($search) {
            $productsQuery
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('url', 'like', '%' . $search . '%');
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(20)->appends(['sort' => $sort, "search" => $search]);

        return view('dashboard', compact('products', 'sort'));
    }

    public function amazonIndex(Request $request) {
        $sort = $request->input('sort', 'latest');
        $search = $request->input('search', '');

        $productsQuery = Product::query();
        $productsQuery->where('site', 2);

        if ($sort == 'stock_level_asc') {
            $productsQuery->orderBy('stock_level', 'asc');
        } elseif ($sort == 'stock_level_desc') {
            $productsQuery->orderBy('stock_level', 'desc');
        }else if ($sort == 'avaliable_value_asc') {
            $productsQuery->orderBy('existance', 'asc');
        } elseif ($sort == 'avaliable_value_desc') {
            $productsQuery->orderBy('existance', 'desc');
        } elseif ($sort == 'discount_exp_asc') {
            $productsQuery->orderByRaw('STR_TO_DATE(discount_exp, "%Y-%m-%d") asc');
        } elseif ($sort == 'discount_exp_desc') {
            $productsQuery->orderByRaw('STR_TO_DATE(discount_exp, "%Y-%m-%d") desc');
        } elseif ($sort == 'discount_value_asc') {
            $productsQuery->orderByRaw('CAST(discount_value AS DECIMAL(10,2)) asc');
        } elseif ($sort == 'discount_value_desc') {
            $productsQuery->orderByRaw('CAST(discount_value AS DECIMAL(10,2)) desc');
        } elseif ($search) {
            $productsQuery
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('url', 'like', '%' . $search . '%');
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(20)->appends(['sort' => $sort, "search" => $search]);

        return view('amazon', compact('products', 'sort'));
    }

    public function storeProduct(Request $request) {
        if ($request->site == 1)
            return CostcoScraper::insertProduct($request);
        if ($request->site == 2)
            return AmazonController::insertProduct($request);
    }

    public function checkWarnings(Request $request)
    {
        $lastChecked = $request->query('lastChecked');

        $warnings = Warning::with("product")->where('created_at', '>', $lastChecked)->where("hide", false)->get();

        return response()->json($warnings);
    }
    public function removeWarning(Request $request)
    {
        $id = $request->query('id');

        $warning = Warning::find($id);
        $warning->hide = true;
        $warning->save();

        return response()->json([
            "status" => true
        ]);
    }
    public function removeWarningEver($id)
    {
        $warning = Warning::find($id);
        $warning->delete();
        return redirect()->back();
    }
    public function destroyAll()
    {
        $warnings = Warning::all();
        foreach ($warnings as $warning) {
            $warning->hide = true;
            $warning->save();
        }
        return response()->json([
            "status" => true
        ]);
    }
    public function removeProduct(Request $request) {
        $product = Product::find($request->id);
        $warnings = $product->warnings()->get();
        foreach ($warnings as $warning) {
            $warning->delete();
        }
        $product->delete();

        return redirect()->back()
        ->with('success', 'Product deleted successfuly');
    }

    public function deletSelected(Request $request) {
        $products = $request->input("products", []);
        foreach ($products as $id) {
            $product = Product::find($id);
            $warnings = $product->warnings()->get();
            foreach ($warnings as $warning) {
                $warning->delete();
            }
            $product->delete();
        }

        return redirect()->back()
        ->with('success', 'Products deleted successfuly');
    }

    public function warningsIndex() {
        return view("notifications");
    }
}
