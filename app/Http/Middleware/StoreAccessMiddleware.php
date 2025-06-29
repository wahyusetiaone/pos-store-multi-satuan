<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Owner dan purchasing dapat mengakses semua toko
        if (in_array($user->role, ['owner', 'purchasing'])) {
            return $next($request);
        }

        // Cek apakah user memiliki akses ke toko yang diminta
        $storeId = $request->route('store') ?? $request->input('store_id') ?? session('current_store_id');

        if (!$storeId) {
            return redirect()->route('store.select');
        }

        // Cek apakah user memiliki akses ke toko ini
        if (!$user->stores()->where('store_id', $storeId)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke toko ini.');
        }

        // Set current store di session
        session(['current_store_id' => $storeId]);

        return $next($request);
    }
}
