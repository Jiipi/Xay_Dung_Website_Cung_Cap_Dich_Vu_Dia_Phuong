<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->vaiTroNguoiDung?->ten_vai_tro !== 'Khách hàng') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bạn không có quyền sử dụng chức năng dành cho khách hàng.',
                );
        }

        return $next($request);
    }
}
