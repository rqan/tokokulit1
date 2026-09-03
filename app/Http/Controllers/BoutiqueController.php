<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    public function index()
    {
        $boutiques = Boutique::all();
        return view('boutiques.index', compact('boutiques'));
    }
}
