<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class AuthService
{
    /**
     * Durasi maksimal sesi login dalam menit (15 jam = 900 menit)
     */
    const MAX_SESSION_TIME = 15 * 60;

    /**
     * Mengecek apakah pengguna sudah login dan apakah sesi masih berlaku.
     * Jika belum login atau waktu telah habis, arahkan ke halaman login.
     *
     * @return void
     */
    public static function checkLogin()
    {
        self::logCheck();

        $isLoggedIn = session('is_logged_in');
        $loginTime = session('login_time');

        if (!$isLoggedIn) {
            self::forceLogout('Anda harus login terlebih dahulu.');
            return;
        }

        // Konversi login_time menjadi Carbon dengan zona waktu Asia/Jakarta
        if (is_string($loginTime)) {
            $loginTime = Carbon::createFromFormat('Y-m-d H:i:s', $loginTime, 'Asia/Jakarta');
        }

        // Pastikan now() juga dalam zona waktu Asia/Jakarta
        $currentTime = Carbon::now('Asia/Jakarta');

        // Hitung total waktu berlalu sejak login (dalam menit)
        $minutesSinceLogin = $loginTime->diffInMinutes($currentTime);

        Log::info('loginTime: ' . $loginTime);

        // Jika waktu yang berlalu melebihi batas
        if ($minutesSinceLogin >= self::MAX_SESSION_TIME) {
            self::forceLogout('Sesi Anda telah berakhir. Silakan login kembali.');
            return;
        }

        // Hitung waktu sisa
        $remainingMinutes = self::MAX_SESSION_TIME - $minutesSinceLogin;

        Log::info('Waktu sisa sesi login: ' . $remainingMinutes . ' menit.');
    }

    /**
     * Memaksa logout pengguna dan mengarahkan ke halaman login dengan pesan error.
     *
     * @param string $message
     * @return void
     */
    private static function forceLogout($message)
    {
        Log::info('Force logging out...');
        session()->flush();
        Redirect::route('login')->with('error', $message)->send();
    }

    /**
     * Mencatat pemanggilan checkLogin di log dan menambah counter di session.
     *
     * @return void
     */

     // Sementara dipakai, nanti bisa dihapus setelah dipublish
    private static function logCheck()
    {
        $checkCount = session('check_login_count', 0) + 1;
        session(['check_login_count' => $checkCount]);
        Log::info('AuthService::checkLogin() dipanggil. Total: ' . $checkCount);
    }
}
