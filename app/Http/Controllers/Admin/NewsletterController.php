<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletter.index', ['title' => 'Manajemen Newsletter - Panel Admin']);
    }

    public function create()
    {
        return view('admin.newsletter.create', ['title' => 'CREATE NEWSLETTER']);
    }

    public function store(Request $request)
    {
        // Store logic...
        return redirect('/admin/newsletter')->with('success', 'Newsletter disimpan.');
    }

    public function send(Request $request, $id)
    {
        // Send logic...
        return redirect('/admin/newsletter')->with('success', 'Newsletter terkirim.');
    }
}
