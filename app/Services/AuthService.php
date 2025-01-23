<?php

namespace App\Services;

use Illuminate\Support\Facades\Redirect;

class AuthService
{
    /**
     * Durasi maksimal dalam satu sesi login (dalam menit)
     */
    const MAX_SESSION_TIME = 45;

    /**
     * Mengecek apakah pengguna sudah login.
     * Jika belum login atau waktu login sudah melebihi batas, arahkan ke halaman login.
     *
     * @return void
     */
    public static function checkLogin()
    {
        $isLoggedIn = session('is_logged_in');
        $loginTime = session('login_time');

        // Jika pengguna belum login
        if (!$isLoggedIn) {
            self::logoutWithRedirect('Anda harus login terlebih dahulu.');
        }

        // Jika waktu login sudah melebihi batas waktu
        if ($loginTime && now()->diffInMinutes($loginTime) > self::MAX_SESSION_TIME) {
            self::logoutWithRedirect('Sesi Anda telah berakhir. Silakan login kembali.');
        }
    }

    /**
     * Logout pengguna dan arahkan ke halaman login dengan pesan error.
     *
     * @param string $message
     * @return void
     */
    private static function logoutWithRedirect($message)
    {
        session()->flush(); // Hapus semua data session
        Redirect::route('login')->with('error', $message)->send();
    }
}
