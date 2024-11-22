<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartPageController extends Controller
{
    public function index()
    {
        $products = Session::get('cart', []);
        // $products = collect($products);
        return view('pages.cart', compact('products'));
    }
}
