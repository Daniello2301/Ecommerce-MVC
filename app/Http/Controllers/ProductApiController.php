<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductApiController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api')->only(['getById']);
        //$this->middleware(['client.credentials'])->only(['index']);
    }

    public function index() {
        $products = Product::with(['Category', 'Brand', 'Seller'])->get();
        return response()->json($products, 200);
    }

    public function getById($id) {
        $product = Product::with(['Category', 'Brand', 'Seller'])
                            ->where('id', $id)
                            ->first();

        if (empty($product)) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($product, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'price' => ['required', 'integer'],
            'inventory' => ['required', 'integer'],
            'image' => 'required|string|max:255',
            'category_id' => ['required', 'integer'],
            'brand_id' => ['required', 'integer'],
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'inventory' => $request->inventory,
            'image' =>  $request->image,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'seller_id' => $request->seller_id,
        ]);

        return Response()->json($product);
    }
}
