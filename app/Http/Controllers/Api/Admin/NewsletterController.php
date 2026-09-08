<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\NewsletterCampaign;
use App\Mail\NewsletterBlast;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    // GET Subscribers List
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subscribers = Subscriber::when($search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return response()->json($subscribers);
    }

    public function destroy($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        return response()->json(['message' => 'Subscriber berhasil dihapus']);
    }

    // GET Campaigns List
    public function campaigns()
    {
        $campaigns = NewsletterCampaign::with('creator:id,name,email')->latest()->get();
        return response()->json($campaigns);
    }

    // POST Create Campaign (Superadmin Only)
    public function storeCampaign(Request $request)
    {
        if ($request->session()->get('role') !== 'superadmin' && optional(Auth::user())->role !== 'superadmin') {
            return response()->json(['message' => 'Akses ditolak: Hanya Superadmin yang bisa membuat News.'], 403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $campaign = NewsletterCampaign::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => 'draft',
            'created_by' => Auth::id()
        ]);

        return response()->json(['message' => 'Newsletter berhasil dibuat', 'campaign' => $campaign], 201);
    }

    // POST Blast Email (Admin & Superadmin)
    public function blast(Request $request, $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status === 'sent') {
            return response()->json(['message' => 'Newsletter ini sudah pernah di-blast.'], 400);
        }

        // Get all subscribers
        $subscribers = Subscriber::all();

        // In production, this should be dispatched to a Queue.
        // For demonstration, we'll send it directly (or simulate it if mail not configured).
        try {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->send(new NewsletterBlast($campaign));
            }

            $campaign->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return response()->json(['message' => 'Email berhasil dikirim ke ' . $subscribers->count() . ' subscribers.']);
        } catch (\Exception $e) {
            Log::error('Newsletter blast error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat mengirim email. Cek log server.'], 500);
        }
    }
}
