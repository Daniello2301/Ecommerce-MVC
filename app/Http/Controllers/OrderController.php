<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = ProductOrder::with(['Order', 'Product'])->get();
        return response()->json($orders, 200);
    }

    public function getByEmail($email){

        $order = Order::where('email', $email)->first();

        $orders = ProductOrder::with(['Order', 'Product'])->where('order_id', $order->id)->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        if($request->email == null){return response()->json("Email is required");}
        if($request->products == null){return response()->json("Products ir necessary");}

        $order = Order::where('email', $request->email)->first();

        if($order == null){
            $order = new Order();
            $order->email = $request->email;
            $order->save();
        }

        $products = $request->products;
        $productsData = [];

        foreach ($products as $product) {
            $productsData[] = $product;
        }

        for ($i = 0; $i < count($productsData); $i++) {
            if($productsData[$i]["id"] == null){return response()->json("Id is required");}
            if($productsData[$i]["quantity"] == null){return response()->json("Quantity is required");}

            $productFound = Product::find($productsData[$i]['id']);
            if($productFound == null)
            {
                return "Product no found";
            }
            if($productsData[$i]['quantity'] > $productFound->inventory)
            {
                return "Error in stock, verify quantity in order";
            }

            $newOrder = new ProductOrder();

            $newOrder->order_id = $order->id;
            $newOrder->product_id = $productsData[$i]['id'];
            $newOrder->total = $productFound->price * $productsData[$i]['quantity'];
            $newOrder->quantity = $productsData[$i]['quantity'];
            $newOrder->save();

            $productFound->name = $productFound->name;
            $productFound->description = $productFound->description;
            $productFound->price = $productFound->price;
            $productFound->inventory = $productFound->inventory - $productsData[$i]['quantity'];
            $productFound->image = $productFound->image;
            $productFound->category_id = $productFound->category_id;
            $productFound->brand_id = $productFound->brand_id;
            $productFound->seller_id = $productFound->seller_id;

            $productFound->save();

        }
        echo $newOrder;
        /* echo $productFound; */

        return Response()->json("Success", 201);
    }
}
