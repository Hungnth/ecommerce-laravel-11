<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AddToCartController extends Controller
{
    public array $cart = [];

    function __construct()
    {
        $this->cart = Session::get('cart', []);
    }

    public function store(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->cart[$product->id] = [
            'id' => $product->id,
            'image' => $product->image,
            'name' => $product->name,
            'price' => $product->price,
            'color' => $request->color,
            'qty' => $request->qty,
        ];

        Session::put('cart', $this->cart);

        return response([
            'status' => 'success',
            'message' => 'Product added to cart successfully!',
            'cart_count' => count($this->cart),
        ]);
    }

    public function destroy($id) {
        $cartItems = $this->cart;
        unset($cartItems[$id]);
        Session::put('cart', $cartItems);
        notyf('Product removed from cart!', 'success');

        return redirect()->back();
    }

    public function updateQty(Request $request) {
        $cartItems = $this->cart;
        $cartItems[$request->id]['qty'] = $request->qty;
        Session::put('cart', $cartItems);
        notyf('Product quantity updated!', 'success');

        return response([
            'status' => 'success'
        ]);
    }
}
