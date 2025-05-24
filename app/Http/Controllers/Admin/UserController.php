<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\UserNotificationMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        // Sadece admin kullanıcıların erişimine izin ver
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Yetkiniz yok');
            }
            return $next($request);
        });
    }

    // Tüm kullanıcıları listele
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('role', 'desc')->get(); // Kendisi hariç tüm kullanıcıları getir, yöneticiler üstte
        return view('admin.users.index', compact('users'));
    }

    // Kullanıcıyı onayla
    public function approve(User $user)
    {
        $user->approved = true;
        $user->save();

        // Kullanıcıya onay e-postası gönder
        try {
            Mail::to($user->email)->send(new UserNotificationMail($user, 'approved'));
        } catch (\Exception $e) {
            // E-posta gönderilemese bile işleme devam et
            report($e);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla onaylandı.');
    }

    // Kullanıcıyı engelle
    public function block(User $user)
    {
        $user->approved = false;
        $user->save();

        return back()->with('success', 'Kullanıcı engellendi.');
    }

    public function makeAdmin(User $user)
    {
        $user->update(['role' => 'admin']);
        return redirect()->route('admin.users.index')->with('success', "{$user->email} yönetici yapıldı.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
