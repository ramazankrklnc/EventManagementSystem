<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordChangeController extends Controller
{
    /**
     * Şifre değiştirme formunu gösterir
     * GET /password/change adresine gelen isteklerde çalışır.
     */
    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * Şifre değiştirme işlemini gerçekleştirir
     * POST /password/change adresine gelen isteklerde çalışır.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Mevcut şifreyi kontrol et
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mevcut şifre yanlış.']);
        }

        // Şifreyi güncelle
        Auth::user()->update([
            'password' => Hash::make($request->password),
            'password_changed' => true,
        ]);

        return redirect()->route('home')->with('success', 'Şifreniz başarıyla değiştirildi.');
    }
} 