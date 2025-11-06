<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class StoreSettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);

        $settings = StoreSetting::first();
        
        if ($settings) {
            $settings->update($validated);
        } else {
            StoreSetting::create($validated);
        }

        return redirect()->back()->with('success', 'Pengaturan toko berhasil diperbarui');
    }
}