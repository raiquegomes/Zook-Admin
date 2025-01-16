<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductDisplayController extends Controller
{
    public function show()
    {
        return view('products.display');
    }
}
