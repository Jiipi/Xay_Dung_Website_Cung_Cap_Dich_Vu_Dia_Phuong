<?php

namespace App\Http\Middleware;

use App\Models\VaiTroNguoiDung;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsProvider
{
    /**
     * Handle an incoming request.
     *
     * Ensure the authenticated user has the "Nhà cung cấp" role.
     * If not, redirect back to the generic dashboard with an error flash.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->vaiTroNguoiDung?->ten_vai_tro !== 'Nhà cung cấp') {
            return redirect()->route('dashboard')
                ->with('error', 'Bạn không có quyền truy cập khu vực nhà cung cấp.');
        }

        return $next($request);
    }
}
