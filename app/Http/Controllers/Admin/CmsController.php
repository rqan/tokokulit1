<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineStore;
use App\Models\OfflineStore;

class CmsController extends Controller
{
    public function index()
    {
        $onlineStores = OnlineStore::all();
        $offlineStores = OfflineStore::all();
        
        return view('admin.cms.index', [
            'title' => 'CONTENT MANAGEMENT', 
            'onlineStores' => $onlineStores,
            'offlineStores' => $offlineStores
        ]);
    }

    public function update(Request $request)
    {
        // Update Online Stores
        $online = $request->input('online_stores', []);
        foreach ($online as $id => $data) {
            $store = OnlineStore::find($id);
            if ($store) {
                $store->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'url' => $data['url'],
                    'is_active' => isset($data['is_active']) ? true : false,
                ]);
            }
        }
        
        // Update Offline Stores
        $offline = $request->input('offline_stores', []);
        foreach ($offline as $id => $data) {
            $store = OfflineStore::find($id);
            if ($store) {
                $store->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'address' => $data['address'],
                    'is_active' => isset($data['is_active']) ? true : false,
                ]);
            }
        }
        
        return redirect('/admin/cms')->with('success', 'Pengaturan Stores berhasil diperbarui.');
    }
}
