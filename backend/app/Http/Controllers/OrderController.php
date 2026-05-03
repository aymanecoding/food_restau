<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource (Admin only).
     */
    public function index()
    {
        return Order::with('user', 'orderItems.dish')->get();
    }

    /**
     * Display the authenticated user's orders.
     */
    public function myOrders()
    {
        $userId = auth()->id();
        $orders = Order::where('user_id', $userId)
            ->with('orderItems.dish')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.dish_id' => 'required|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_address' => 'required|string|max:500',
            'client_note' => 'nullable|string',
            'payment_method' => 'required|in:cash,card,mobile',
            'total_price' => 'required|numeric|min:0',
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                // Récupérer l'utilisateur connecté Sanctum si disponible
                $userId = Auth::guard('sanctum')->id() ?? null;
                
                $order = Order::create([
                    'user_id' => $userId,
                    'client_name' => $request->client_name,
                    'client_phone' => $request->client_phone,
                    'client_address' => $request->client_address,
                    'client_note' => $request->client_note,
                    'payment_method' => $request->payment_method,
                    'total_price' => $request->total_price,
                    'status' => 'pending',
                ]);

                foreach ($request->items as $item) {
                    $dish = Dish::find($item['dish_id']);
                    if (!$dish) {
                        throw new \Exception("Plat avec ID {$item['dish_id']} introuvable");
                    }
                    OrderItem::create([
                        'order_id' => $order->id,
                        'dish_id' => $item['dish_id'],
                        'quantity' => $item['quantity'],
                        'price' => $dish->price,
                    ]);
                }

                return $order->load('orderItems.dish');
            });

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise pour accéder à cette commande.',
            ], 401);
        }

        if (!$user->is_admin && $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à voir cette commande.',
            ], 403);
        }

        return $order->load('user', 'orderItems.dish');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered',
        ]);

        $order->update($request->only('status'));
        return $order;
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->noContent();
    }
}
