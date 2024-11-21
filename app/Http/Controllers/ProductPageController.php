<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class ProductPageController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('pages.home', compact('products'));
    }

    public function show($id)
    {
        // session()->flush();
        $product = Product::findOrFail($id);
        return view('pages.product-details', compact('product'));
    }
}
