<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah'])->withInput();
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        // Jika ada cropped image (base64)
        if ($request->filled('cropped_image')) {
            // Hapus avatar lama
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                unlink(storage_path('app/public/' . $user->avatar));
            }

            // Decode base64
            $image = $request->cropped_image;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            // Simpan file
            $filename = 'avatars/' . time() . '_avatar.jpg';
            file_put_contents(storage_path('app/public/' . $filename), $imageData);

            // Update user
            \App\Models\User::where('id', $user->id)->update(['avatar' => $filename]);

            return back()->with('success', 'Avatar berhasil diubah');
        }

        // Fallback: upload biasa
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                unlink(storage_path('app/public/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $filename = 'avatars/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(storage_path('app/public/avatars'), basename($filename));
            
            \App\Models\User::where('id', $user->id)->update(['avatar' => $filename]);
        }

        return back()->with('success', 'Avatar berhasil diubah');
    }
}