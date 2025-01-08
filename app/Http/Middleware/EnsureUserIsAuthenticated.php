<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!session('is_logged_in')) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        return $next($request);
    }
}
