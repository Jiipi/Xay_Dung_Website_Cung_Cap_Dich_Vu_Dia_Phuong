<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->vaiTroNguoiDung?->ten_vai_tro !== 'Admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}
