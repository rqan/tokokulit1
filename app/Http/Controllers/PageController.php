<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnlineStore;
use App\Models\OfflineStore;

class PageController extends Controller
{
    public function stores()
    {
        $onlineStores = OnlineStore::where('is_active', true)->get();
        $offlineStores = OfflineStore::where('is_active', true)->get();
        return view('pages.stores', compact('onlineStores', 'offlineStores'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
