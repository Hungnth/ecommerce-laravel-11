<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use File;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.dashboard', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $product = new Product();

        // insert images
        if ($request->hasFile('image')) {
            // store image
            $image = $request->file('image');
            $fileName = $image->store('', 'public');
            $filePath = 'uploads/' . $fileName;
            $product->image = $filePath;
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->short_description = $request->short_description;
        $product->qty = $request->qty;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->save();

        // insert colors
        if ($request->has('colors') && $request->filled('colors')) {
            foreach ($request->colors as $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'name' => $color,
                ]);
            }
        }

        // insert images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // store image
                $fileName = $image->store('', 'public');
                $filePath = 'uploads/' . $fileName;
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $filePath,
                ]);
            }
        }

        notyf('Product created successfully!', 'success');
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['colors', 'images'])->findOrFail($id);
        $colors = $product->colors->pluck('name')->toArray();

        return view('admin.product.edit', compact('product', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        if ($request->hasFile('image')) {
            // delete old image
            File::delete(public_path($product->image));
            $image = $request->file('image');
            $fileName = $image->store('', 'public');
            $filePath = 'uploads/' . $fileName;
            $product->image = $filePath;
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->short_description = $request->short_description;
        $product->qty = $request->qty;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->save();

        // insert colors
        if ($request->has('colors') && $request->filled('colors')) {
            foreach ($request->colors as $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'name' => $color,
                ]);
            }
        }

        if ($request->hasFile('images')) {
            //delete old image
            foreach ($product->image as $image) {
                File::delete(public_path($image->path));
            }
            $product->image()->delete();
            foreach ($request->file('images') as $image) {
                // store image
                $fileName = $image->store('', 'public');
                $filePath = 'uploads/' . $fileName;
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $filePath,
                ]);
            }
        }

        notyf('Product updated successfully!', 'success');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // dd($id);
        $product = Product::findOrFail($id);
        $product->colors()->delete();
        File::delete(public_path($product->image));
        foreach ($product->images as $image) {
            File::delete(public_path($image->path));
        }
        $product->delete();

        notyf('Product deleted successfully!', 'success');
        return redirect()->back();
    }
}
