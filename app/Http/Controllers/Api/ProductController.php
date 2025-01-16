<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        // Forçar o campo 'price' a ser convertido em número
        $products = $products->map(function ($product) {
            $product->price = (float) $product->price;  // Convertendo para float
            $product->image_url = asset('storage/' . $product->image_url);
            return $product;
        });

        return response()->json($products);
    }

    public function productsByCategory()
    {
        // Obtém os produtos agrupados por categoria
        $categoriesWithProducts = Product::with('category') // Certifique-se de que o relacionamento "category" está definido no modelo Product
            ->get()
            ->groupBy(function ($product) {
                return $product->category->name ?? 'Sem Categoria'; // Agrupa pelo nome da categoria
            });

        // Formata os dados para retorno
        $formattedData = $categoriesWithProducts->map(function ($products, $categoryName) {
            return [
                'category' => $categoryName,
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'image_url' => asset('storage/' . $product->image_url),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($formattedData);
    }
}
