<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Ambil role user yang sedang login
        $user = auth()->user();

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            $redirectUrl = route('admin.dashboard');
        } elseif ($user->role === 'product_manager') {
            $redirectUrl = route('productmanager.dashboard');
        } else {
            $redirectUrl = route('dashboard'); // Default ke dashboard umum
        }

        return redirect()->intended($redirectUrl);
    }
}
