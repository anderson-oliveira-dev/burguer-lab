<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->type === 'admin') {
            $query = Order::with('items.product', 'user');
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            return response()->json($query->orderBy('created_at', 'desc')->get());
        }

        $orders = Order::with('items.product')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        if ($request->user()->type !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'type'            => ['required', Rule::in(['delivery', 'pickup'])],
            'address'         => 'required_if:type,delivery|nullable|string|max:255',
            'payment_method'  => ['required', Rule::in(['cash', 'card', 'pix'])],
            'observations'    => 'nullable|string|max:500',
            'delivery_fee'    => 'nullable|numeric|min:0',
        ]);

        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio'], 422);
        }

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->user_id = $user->id;
            $order->type = $validated['type'];
            $order->address = $validated['type'] === 'delivery' ? $validated['address'] : null;
            $order->phone = $user->phone;
            $order->payment_method = $validated['payment_method'];
            $order->status = 'awaiting_confirmation';
            $order->delivery_fee = $validated['delivery_fee'] ?? 0;
            $order->observations = $validated['observations'] ?? null;
            $order->total_price = 0;
            $order->save();

            $total = 0;

            foreach ($cart->items as $cartItem) {
                $extras = $cartItem->extras ?? [];
                $extrasTotal = collect($extras)->sum('price') ?? 0;
                $unitPrice = $cartItem->unit_price ?? $cartItem->product->price;
                $subtotal = ($unitPrice + $extrasTotal) * $cartItem->quantity;

                $orderItem = new OrderItem();
                $orderItem->order_id   = $order->id;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->quantity   = $cartItem->quantity;
                $orderItem->unit_price = $unitPrice;
                $orderItem->extras     = $extras;
                $orderItem->subtotal   = $subtotal;
                $orderItem->save();

                $total += $subtotal;
            }

            $order->total_price = $total + $order->delivery_fee;
            $order->save();

            $cart->items()->delete();

            DB::commit();

            $order->load('items.product');

            return response()->json([
                'message' => 'Pedido criado com sucesso!',
                'order'   => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if ($user->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'awaiting_confirmation',
                'preparing',
                'ready_for_delivery',
                'delivered',
                'canceled'
            ])],
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'message' => 'Status atualizado',
            'order'   => $order,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();

        if ($user->type !== 'admin' && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($order->status, ['awaiting_confirmation', 'preparing'])) {
            return response()->json(['message' => 'Pedido não pode ser cancelado nesse estágio'], 422);
        }

        $order->status = 'canceled';
        $order->save();

        return response()->json(['message' => 'Pedido cancelado', 'order' => $order]);
    }
}