<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Services\AuditService;

class ReviewController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Menampilkan daftar ulasan.
     */
    public function index()
    {
        $ratings = Rating::with(['user', 'order'])->latest()->get();
        return view('admin.reviews.index', ['reviews' => $ratings]);
    }

    /**
     * Membalas ulasan.
     */
    public function reply(Request $request, $id)
    {
        return back()->with('success', 'Balasan berhasil dikirim (Fitur ini sedang dalam pengembangan).');
    }

    /**
     * Menghapus ulasan secara soft delete.
     */
    public function delete($id)
    {
        if (session('role') !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menghapus ulasan.');
        }

        $rating = Rating::findOrFail($id);
        $oldData = $rating->toArray();
        $rating->delete();
        
        $this->auditService->log('delete_rating', 'Rating', $rating->id, $oldData, []);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
