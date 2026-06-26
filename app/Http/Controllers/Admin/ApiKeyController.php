<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = ApiKey::with('user')->latest()->paginate(20);
        return view('admin.api-keys.index', compact('keys'));
    }

    public function create()
    {
        return view('admin.api-keys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'allowed_ips' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'key' => ApiKey::generateKey(),
            'allowed_ips' => $request->allowed_ips ? array_map('trim', explode(',', $request->allowed_ips)) : null,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key berhasil dibuat! Simpan key ini: <strong>' . $apiKey->key . '</strong>');
    }

    public function show(ApiKey $apiKey)
    {
        return view('admin.api-keys.show', compact('apiKey'));
    }

    public function edit(ApiKey $apiKey)
    {
        return view('admin.api-keys.edit', compact('apiKey'));
    }

    public function update(Request $request, ApiKey $apiKey)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'allowed_ips' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $apiKey->update([
            'name' => $request->name,
            'allowed_ips' => $request->allowed_ips ? array_map('trim', explode(',', $request->allowed_ips)) : null,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key berhasil diupdate');
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key berhasil dihapus');
    }

    public function toggle($id)
    {
        $key = ApiKey::findOrFail($id);
        $key->update(['is_active' => !$key->is_active]);
        return back()->with('success', 'Status API Key berhasil diubah');
    }
}