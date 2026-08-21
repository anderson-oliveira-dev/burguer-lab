<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->available()
            ->get();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'image'         => 'nullable|url|max:2048',
            'available'     => 'sometimes|boolean',
        ]);

        $categoryId = $validated['category_id'] ?? null;
        if (empty($categoryId) && !empty($validated['category_name'])) {
            $category = Category::where('name', $validated['category_name'])->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        $product = Product::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'category_id' => $categoryId,
            'image'       => $validated['image'] ?? null,
            'available'   => $validated['available'] ?? true,
        ]);

        return new ProductResource($product);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'sometimes|required|numeric|min:0',
            'category_id'   => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'image'         => 'nullable|url|max:2048',
            'available'     => 'sometimes|boolean',
        ]);

        $categoryId = $validated['category_id'] ?? null;
        if (empty($categoryId) && !empty($validated['category_name'])) {
            $category = Category::where('name', $validated['category_name'])->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        $product->fill([
            'name'        => $validated['name'] ?? $product->name,
            'description' => $validated['description'] ?? $product->description,
            'price'       => $validated['price'] ?? $product->price,
            'category_id' => $categoryId ?? $product->category_id,
            'image'       => $validated['image'] ?? $product->image,
            'available'   => $validated['available'] ?? $product->available,
        ]);

        $product->save();

        return new ProductResource($product);
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produto removido com sucesso.']);
    }
}
