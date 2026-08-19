<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart; // se você tiver model Cart
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Listar pedidos do usuário autenticado (cliente) ou todos (admin)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->type === 'admin') {
            // Admin pode filtrar por status via query param
            $query = Order::with('items.product', 'user');
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            return response()->json($query->orderBy('created_at', 'desc')->get());
        }

        // Cliente vê apenas seus pedidos
        $orders = Order::with('items.product')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($orders);
    }

    /**
     * Detalhes de um pedido específico
     */
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        // Verifica se o usuário tem permissão (admin ou dono do pedido)
        if ($request->user()->type !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order);
    }

    /**
     * Cria um novo pedido a partir do carrinho atual do usuário
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Valida os dados do checkout
        $validated = $request->validate([
            'type'            => ['required', Rule::in(['delivery', 'pickup'])],
            'address'         => 'required_if:type,delivery|nullable|string|max:255',
            'payment_method'  => ['required', Rule::in(['cash', 'card', 'pix'])],
            'observations'    => 'nullable|string|max:500',
            'delivery_fee'    => 'nullable|numeric|min:0',
        ]);

        // Busca o carrinho do usuário
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio'], 422);
        }

        // Inicia transação para garantir consistência
        DB::beginTransaction();

        try {
            // Cria o pedido
            $order = new Order();
            $order->user_id = $user->id;
            $order->type = $validated['type'];
            $order->address = $validated['type'] === 'delivery' ? $validated['address'] : null;
            $order->phone = $user->phone; // pré-preenche com o telefone do usuário
            $order->payment_method = $validated['payment_method'];
            $order->status = 'awaiting_confirmation'; // sempre começa assim
            $order->delivery_fee = $validated['delivery_fee'] ?? 0;
            $order->observations = $validated['observations'] ?? null;
            // O total será calculado a partir dos itens
            $order->total_price = 0;
            $order->save();

            $total = 0;

            // Copia cada item do carrinho para order_items
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

            // Atualiza o total do pedido (incluindo taxa de entrega)
            $order->total_price = $total + $order->delivery_fee;
            $order->save();

            // Limpa o carrinho após criar o pedido
            $cart->items()->delete();

            DB::commit();

            // Retorna o pedido criado com seus itens
            $order->load('items.product');

            return response()->json([
                'message' => 'Pedido criado com sucesso!',
                'order'   => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // ou retornar erro 500 com mensagem
        }
    }

    /**
     * Atualiza o status do pedido (apenas admin)
     */
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

    /**
     * Cancelar pedido (pode ser feito pelo admin ou pelo próprio cliente)
     * Vamos permitir cancelar apenas se estiver 'awaiting_confirmation' ou 'preparing'
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = $request->user();

        // Admin ou dono podem cancelar
        if ($user->type !== 'admin' && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Só pode cancelar se ainda não foi entregue
        if (!in_array($order->status, ['awaiting_confirmation', 'preparing'])) {
            return response()->json(['message' => 'Pedido não pode ser cancelado nesse estágio'], 422);
        }

        $order->status = 'canceled';
        $order->save();

        return response()->json(['message' => 'Pedido cancelado', 'order' => $order]);
    }
}