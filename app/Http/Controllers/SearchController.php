<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $q = $request->input('q', '');

        $results = collect();

        if (strlen(trim($q)) >= 2) {
            $userFavorites = auth()->check() 
                ? \App\Models\YeuThich::where('nguoi_dung_id', auth()->id())->pluck('dich_vu_id')->toArray() 
                : [];

            $results = DichVu::query()
                ->where('trang_thai_hoat_dong', 'hoat_dong')
                ->where(function ($query) use ($q) {
                    $query->where('ten_dich_vu', 'ILIKE', "%{$q}%")
                          ->orWhere('mo_ta_chi_tiet', 'ILIKE', "%{$q}%")
                          ->orWhere('dia_chi_hien_thi', 'ILIKE', "%{$q}%");
                })
                ->with(['nhaCungCap.hoSoNhaCungCap', 'danhMuc'])
                ->limit(20)
                ->get()
                ->map(function ($s) use ($userFavorites) {
                    $image = (is_array($s->danh_sach_anh) && count($s->danh_sach_anh) > 0)
                        ? $s->danh_sach_anh[0]
                        : 'https://picsum.photos/seed/' . md5($s->id) . '/600/400';

                    return [
                        'id'       => $s->id,
                        'title'    => $s->ten_dich_vu,
                        'slug'     => $s->slug,
                        'image'    => $image,
                        'price'    => (int) $s->gia_tu,
                        'rating'   => round((float) ($s->nhaCungCap?->hoSoNhaCungCap?->diem_danh_gia ?? 0), 1),
                        'reviews'  => 0,
                        'location' => $s->dia_chi_hien_thi,
                        'provider' => $s->nhaCungCap?->hoSoNhaCungCap?->ten_thuong_hieu ?? $s->nhaCungCap?->name,
                        'category' => $s->danhMuc?->ten_danh_muc,
                        'is_favorited' => in_array($s->id, $userFavorites),
                    ];
                });
        }

        return Inertia::render('search/Index', [
            'query'   => $q,
            'results' => $results,
            'total'   => $results->count(),
        ]);
    }
}
