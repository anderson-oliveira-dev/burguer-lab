<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Retorna o carrinho atual com itens e total
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        if (!$cart) {
            return response()->json(['items' => [], 'total' => 0]);
        }

        $cart->load('items.product');

        $items = $cart->items->map(function ($item) {
            $item->subtotal = $item->subtotal;

            if ($item->product) {
                $item->product->image = $item->product->image_url;
            }

            return $item;
        });

        $total = $items->sum('subtotal');

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }

    /**
     * Adiciona um item ao carrinho
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'extras'     => 'array',
            'extras.*.id'    => 'integer',
            'extras.*.name'  => 'string',
            'extras.*.price' => 'numeric',
        ]);

        $cart = $this->getCart($request, true);

        $product = Product::findOrFail($validated['product_id']);
        $extras = $validated['extras'] ?? [];

        // Verifica se já existe item idêntico (mesmo produto e extras)
        $existingItem = $this->findSimilarItem($cart, $validated['product_id'], $extras);

        if ($existingItem) {
            $existingItem->quantity += $validated['quantity'];
            $existingItem->save();
        } else {
            $item = new CartItem();
            $item->cart_id = $cart->id;
            $item->product_id = $validated['product_id'];
            $item->quantity = $validated['quantity'];
            $item->unit_price = $product->price;
            $item->extras = $extras;
            $item->save();
        }

        return $this->index($request);
    }

    /**
     * Atualiza a quantidade de um item
     */
    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($itemId);
        $this->authorizeCartItem($item, $request); // passa o request

        $item->quantity = $validated['quantity'];
        $item->save();

        return $this->index($request);
    }

    /**
     * Remove um item do carrinho
     */
    public function destroy(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $this->authorizeCartItem($item, $request);
        $item->delete();

        return response()->json(['message' => 'Item removido']);
    }

    /**
     * Limpa todo o carrinho
     */
    public function clear(Request $request)
    {
        $cart = $this->getCart($request);

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Carrinho limpo']);
    }

    /**
     * Sincroniza (merge) carrinho local após login
     * (mantido para uso futuro com autenticação)
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.extras' => 'array',
            'items.*.extras.*.id' => 'integer',
            'items.*.extras.*.name' => 'string',
            'items.*.extras.*.price' => 'numeric',
        ]);

        // Obtém o carrinho do usuário autenticado (cria se não existir)
        $cart = $this->getCart($request, true);

        // Processa cada item recebido
        foreach ($validated['items'] as $itemData) {
            $product = Product::find($itemData['product_id']);
            $extras = $itemData['extras'] ?? [];
            $extrasJson = $this->normalizeExtras($extras);

            // Procura um item existente com mesmo produto e extras
            $existingItem = null;
            foreach ($cart->items as $item) {
                $itemExtrasJson = $this->normalizeExtras($item->extras);
                if ($item->product_id == $itemData['product_id'] && $itemExtrasJson === $extrasJson) {
                    $existingItem = $item;
                    break;
                }
            }

            if ($existingItem) {
                // Soma a quantidade
                $existingItem->quantity += $itemData['quantity'];
                $existingItem->save();
            } else {
                // Cria novo item
                $item = new CartItem();
                $item->cart_id = $cart->id;
                $item->product_id = $itemData['product_id'];
                $item->quantity = $itemData['quantity'];
                $item->unit_price = $product->price;
                $item->extras = $extras;
                $item->save();
            }
        }

        // Remove o carrinho do convidado (se existir e for diferente)
        $guestId = $request->header('X-Guest-Id') ?? $request->input('guest_id');
        if ($guestId && auth()->check()) {
            $guestCart = Cart::where('guest_id', $guestId)->whereNull('user_id')->first();
            if ($guestCart && $guestCart->id != $cart->id) {
                $guestCart->delete();
            }
        }

        // Retorna o carrinho atualizado
        return $this->index($request);
    }

    // --- Métodos auxiliares ---

    /**
     * Obtém o carrinho do usuário autenticado ou do convidado (via guest_id)
     */
    private function getCart(Request $request, $createIfMissing = false)
    {
        $userId = auth()->id();
        $guestId = $request->header('X-Guest-Id') ?? $request->input('guest_id');

        // Se não houver identificador de convidado e o usuário não estiver autenticado,
        // podemos gerar um novo guest_id (ou retornar null)
        if (!$userId && !$guestId) {
            // Opção: gerar um novo guest_id no servidor e retornar? 
            // Melhor: o cliente deve enviar um UUID gerado por ele.
            // Lançar exceção ou retornar null.
            return null;
        }

        $cart = null;
        if ($userId) {
            $cart = Cart::where('user_id', $userId)->first();
        } elseif ($guestId) {
            $cart = Cart::where('guest_id', $guestId)->first();
        }

        if (!$cart && $createIfMissing) {
            $cart = new Cart();
            if ($userId) {
                $cart->user_id = $userId;
            } else {
                $cart->guest_id = $guestId;
            }
            $cart->save();
        }

        return $cart;
    }

    /**
     * Encontra item similar (mesmo produto e extras)
     */
    private function findSimilarItem($cart, $productId, $extras)
    {
        $extrasJson = $this->normalizeExtras($extras);

        foreach ($cart->items as $item) {
            $itemExtrasJson = $this->normalizeExtras($item->extras);
            if ($item->product_id == $productId && $itemExtrasJson === $extrasJson) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Normaliza extras para comparação JSON (ordena por id)
     */
    private function normalizeExtras($extras)
    {
        if (empty($extras)) {
            return '[]';
        }
        $sorted = collect($extras)->sortBy('id')->values()->toArray();
        return json_encode($sorted);
    }

    /**
     * Verifica se o item pertence ao carrinho do usuário atual ou convidado
     */
    private function authorizeCartItem($item, Request $request)
    {
        $cart = $item->cart;
        $userId = auth()->id();
        $guestId = $request->header('X-Guest-Id') ?? $request->input('guest_id');

        if ($cart->user_id && $cart->user_id != $userId) {
            abort(403, 'Unauthorized');
        }
        if ($cart->guest_id && $cart->guest_id != $guestId) {
            abort(403, 'Unauthorized');
        }
        // Se ambos são null, algo está errado
        if (!$cart->user_id && !$cart->guest_id) {
            abort(403, 'Invalid cart');
        }
    }
}