<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Commandes;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Commandes::where('user_id', $request->user()->id)
            ->with('produits.photos')
            ->withCount('produits')
            ->latest()
            ->paginate((int) $request->query('per_page', 15));

        return OrderResource::collection($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Commandes::where('user_id', $request->user()->id)
            ->with('produits.photos')
            ->withCount('produits')
            ->findOrFail($id);

        return new OrderResource($order);
    }
}
