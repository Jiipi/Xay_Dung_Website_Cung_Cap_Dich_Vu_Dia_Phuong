<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = parent::handle($request, $next);

        if (
            app()->environment(['local', 'testing'])
            && $request->isMethod('GET')
            && (
                $request->headers->has('X-Inertia')
                || str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            )
        ) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'role' => $request->user()?->vaiTroNguoiDung?->ten_vai_tro,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'providerProfile' => function () use ($request) {
                $user = $request->user();
                if (! $user) return null;
                $profile = $user->hoSoNhaCungCap;
                if (! $profile) return null;
                return [
                    'ten_thuong_hieu' => $profile->ten_thuong_hieu,
                    'diem_danh_gia' => (float) $profile->diem_danh_gia,
                ];
            },
            'unreadNotifications' => function () use ($request) {
                $user = $request->user();
                if (! $user) return 0;
                return \App\Models\ThongBao::where('nguoi_dung_id', $user->id)
                    ->where('da_doc', false)
                    ->count();
            },
            'pendingBookingsCount' => function () use ($request) {
                $user = $request->user();
                if (! $user) return 0;
                $role = $user->vaiTroNguoiDung?->ten_vai_tro;
                if ($role !== 'Nhà cung cấp') return 0;
                return \App\Models\DonDatLich::where('nha_cung_cap_id', $user->id)
                    ->where('trang_thai_don', 'cho_xac_nhan')
                    ->count();
            },
        ];
    }
}
