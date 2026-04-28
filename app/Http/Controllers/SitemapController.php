<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Model\Product;
use App\Model\Category;
class SitemapController extends Controller
{
    public function index()
    {
        // Get all active products and categories
        $products = Product::all(); 
        $categories = Category::all();

        return response()->view('sitemap', [
            'products' => $products,
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
