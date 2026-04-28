<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\DanhMucDichVu;
use App\Models\DichVu;
use App\Models\HoSoNhaCungCap;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Phục vụ Danh mục (lấy 8 danh mục nổi bật)
        $categoriesModel = DanhMucDichVu::whereNull('parent_id')->take(8)->get();

        // Bảng màu pastel riêng biệt cho từng danh mục
        $accentPalette = [
            ['from-sky-100 to-cyan-100 text-sky-700', 'bg-sky-50', '#0ea5e9'],
            ['from-amber-100 to-yellow-100 text-amber-700', 'bg-amber-50', '#f59e0b'],
            ['from-emerald-100 to-green-100 text-emerald-700', 'bg-emerald-50', '#10b981'],
            ['from-violet-100 to-purple-100 text-violet-700', 'bg-violet-50', '#8b5cf6'],
            ['from-rose-100 to-pink-100 text-rose-700', 'bg-rose-50', '#f43f5e'],
            ['from-teal-100 to-cyan-100 text-teal-700', 'bg-teal-50', '#14b8a6'],
            ['from-orange-100 to-amber-100 text-orange-700', 'bg-orange-50', '#f97316'],
            ['from-indigo-100 to-blue-100 text-indigo-700', 'bg-indigo-50', '#6366f1'],
        ];

        $iconMapping = [
            'du-lich' => 'Map',
            'sua-chua' => 'Wrench',
            've-sinh' => 'Sparkles',
            'thue-xe' => 'CarFront',
            'dien-lanh' => 'Snowflake',
            'son-nha' => 'Paintbrush',
            'may-tinh' => 'Monitor',
            'lam-dep' => 'Heart',
        ];

        $categories = $categoriesModel->values()->map(function ($cat, $index) use ($accentPalette, $iconMapping) {
            $icon = 'Search';
            foreach ($iconMapping as $keyword => $iconName) {
                if (str_contains($cat->slug, $keyword)) {
                    $icon = $iconName;
                    break;
                }
            }

            $palette = $accentPalette[$index % count($accentPalette)];

            return [
                'id' => $cat->slug,
                'name' => $cat->ten_danh_muc,
                'description' => $cat->mo_ta ?? 'Khám phá các dịch vụ hàng đầu trong danh mục này.',
                'icon' => $icon,
                'accent' => $palette[0],
                'bg' => $palette[1],
                'color' => $palette[2],
                'service_count' => $cat->dichVu()->count(),
            ];
        });

        // Fallback nếu DB trống
        if ($categories->isEmpty()) {
            $fallbackCategories = [
                ['id' => 'du-lich', 'name' => 'Du lịch & Tour', 'description' => 'Khám phá các tour du lịch hấp dẫn tại Đà Lạt.', 'icon' => 'Map', 'accent' => $accentPalette[0][0], 'bg' => $accentPalette[0][1], 'color' => $accentPalette[0][2], 'service_count' => 15],
                ['id' => 'sua-chua', 'name' => 'Sửa chữa', 'description' => 'Thợ sửa chữa tay nghề cao, phục vụ tận nơi.', 'icon' => 'Wrench', 'accent' => $accentPalette[1][0], 'bg' => $accentPalette[1][1], 'color' => $accentPalette[1][2], 'service_count' => 22],
                ['id' => 've-sinh', 'name' => 'Vệ sinh', 'description' => 'Dịch vụ vệ sinh chuyên nghiệp cho mọi không gian.', 'icon' => 'Sparkles', 'accent' => $accentPalette[2][0], 'bg' => $accentPalette[2][1], 'color' => $accentPalette[2][2], 'service_count' => 18],
                ['id' => 'thue-xe', 'name' => 'Thuê xe', 'description' => 'Thuê xe máy, ô tô giao tận nơi, giá rẻ.', 'icon' => 'CarFront', 'accent' => $accentPalette[3][0], 'bg' => $accentPalette[3][1], 'color' => $accentPalette[3][2], 'service_count' => 12],
                ['id' => 'dien-lanh', 'name' => 'Điện lạnh', 'description' => 'Sửa chữa, vệ sinh điều hòa, tủ lạnh nhanh chóng.', 'icon' => 'Snowflake', 'accent' => $accentPalette[4][0], 'bg' => $accentPalette[4][1], 'color' => $accentPalette[4][2], 'service_count' => 9],
                ['id' => 'son-nha', 'name' => 'Sơn nhà', 'description' => 'Sơn nhà trọn gói, bảo hành dài hạn.', 'icon' => 'Paintbrush', 'accent' => $accentPalette[5][0], 'bg' => $accentPalette[5][1], 'color' => $accentPalette[5][2], 'service_count' => 7],
                ['id' => 'lap-dat', 'name' => 'Lắp đặt', 'description' => 'Lắp đặt thiết bị, nội thất chuyên nghiệp.', 'icon' => 'Monitor', 'accent' => $accentPalette[6][0], 'bg' => $accentPalette[6][1], 'color' => $accentPalette[6][2], 'service_count' => 11],
                ['id' => 'lam-dep', 'name' => 'Làm đẹp', 'description' => 'Chăm sóc sắc đẹp tại nhà, tiện lợi.', 'icon' => 'Heart', 'accent' => $accentPalette[7][0], 'bg' => $accentPalette[7][1], 'color' => $accentPalette[7][2], 'service_count' => 14],
            ];
            $categories = collect($fallbackCategories);
        }

        // 2. Phục vụ Dịch vụ nổi bật
        $servicesModel = DichVu::with(['nhaCungCap.hoSoNhaCungCap'])
            ->where('trang_thai_hoat_dong', 'hoat_dong')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        $userFavorites = auth()->check() 
            ? \App\Models\YeuThich::where('nguoi_dung_id', auth()->id())->pluck('dich_vu_id')->toArray() 
            : [];

        $featuredServices = $servicesModel->map(function ($svc) use ($userFavorites) {
            $hoso = $svc->nhaCungCap?->hoSoNhaCungCap;

            // Format giá
            $priceText = number_format($svc->gia_tu, 0, ',', '.') . 'đ';
            if ($svc->gia_den) {
                $priceText .= ' - ' . number_format($svc->gia_den, 0, ',', '.') . 'đ';
            }
            
            $unit = null;
            if ($svc->don_vi_gia) {
                $unit = trim(str_ireplace(['VND /', 'VND/'], '', $svc->don_vi_gia));
                $unit = trim(str_ireplace('VND', '', $unit));
                $unit = ltrim($unit, '/ ');
            }

            // Tính review count thực từ DB
            $reviewCount = DanhGia::where('nha_cung_cap_id', $svc->nha_cung_cap_id)->count();

            return [
                'id' => $svc->id,
                'title' => $svc->ten_dich_vu,
                'provider' => $hoso ? $hoso->ten_thuong_hieu : 'Nhà cung cấp ẩn danh',
                'price' => $priceText,
                'unit' => $unit,
                'priceRaw' => $svc->gia_tu,
                'rating' => $hoso ? number_format($hoso->diem_danh_gia, 1) : '5.0',
                'reviewCount' => $reviewCount,
                'location' => $svc->dia_chi_hien_thi ?? 'Đà Lạt',
                'duration' => $svc->thoi_gian_thuc_hien ?? '60 phút',
                'tone' => 'from-sky-200 via-white to-cyan-50',
                'image' => (is_array($svc->danh_sach_anh) && count($svc->danh_sach_anh) > 0) 
                            ? $svc->danh_sach_anh[0] 
                            : 'https://picsum.photos/seed/'.$svc->id.'/800/600',
                'badge' => $reviewCount > 100 ? 'Phổ biến' : ($svc->gia_tu < 300000 ? 'Giá tốt' : 'Chất lượng'),
                'is_favorited' => in_array($svc->id, $userFavorites),
            ];
        });

        // Fallback nếu DB services trống
        if ($featuredServices->isEmpty()) {
            $featuredServices = collect([
                ['id' => 1, 'title' => 'Sửa điều hòa chuyên nghiệp', 'provider' => 'Điện Lạnh 24h', 'price' => '250.000đ', 'unit' => null, 'priceRaw' => 250000, 'rating' => '4.9', 'reviewCount' => 156, 'location' => 'Quận 1', 'duration' => '60 phút', 'tone' => 'from-sky-200 via-white to-cyan-50', 'image' => 'https://picsum.photos/seed/ac-repair/800/600', 'badge' => 'Phổ biến'],
                ['id' => 2, 'title' => 'Sơn nhà trọn gói', 'provider' => 'Sơn Đẹp Pro', 'price' => '450.000đ', 'unit' => 'm²', 'priceRaw' => 450000, 'rating' => '4.8', 'reviewCount' => 98, 'location' => 'Quận 5', 'duration' => '180 phút', 'tone' => 'from-amber-200 via-white to-yellow-50', 'image' => 'https://picsum.photos/seed/painting/800/600', 'badge' => 'Chất lượng'],
                ['id' => 3, 'title' => 'Lắp quạt trần chuyên nghiệp', 'provider' => 'Thợ Điện Minh Tâm', 'price' => '300.000đ', 'unit' => null, 'priceRaw' => 300000, 'rating' => '5.0', 'reviewCount' => 45, 'location' => 'Quận 1', 'duration' => '60 phút', 'tone' => 'from-emerald-200 via-white to-green-50', 'image' => 'https://picsum.photos/seed/fan-install/800/600', 'badge' => 'Chất lượng'],
                ['id' => 4, 'title' => 'Tour Săn Mây Tà Xùa 2N1Đ', 'provider' => 'Travel Pro Dalat', 'price' => '1.500.000đ', 'unit' => 'người', 'priceRaw' => 1500000, 'rating' => '4.9', 'reviewCount' => 203, 'location' => 'Đà Lạt', 'duration' => '2 ngày', 'tone' => 'from-violet-200 via-white to-purple-50', 'image' => 'https://picsum.photos/seed/cloud-hunt/800/600', 'badge' => 'Phổ biến'],
                ['id' => 5, 'title' => 'Vệ sinh máy giặt', 'provider' => 'Clean House', 'price' => '200.000đ', 'unit' => null, 'priceRaw' => 200000, 'rating' => '4.7', 'reviewCount' => 87, 'location' => 'Quận 3', 'duration' => '90 phút', 'tone' => 'from-teal-200 via-white to-cyan-50', 'image' => 'https://picsum.photos/seed/washing/800/600', 'badge' => 'Giá tốt'],
                ['id' => 6, 'title' => 'Thuê xe máy Đà Lạt', 'provider' => 'Đà Lạt Motor', 'price' => '120.000đ', 'unit' => 'ngày', 'priceRaw' => 120000, 'rating' => '4.8', 'reviewCount' => 312, 'location' => 'Đà Lạt', 'duration' => '24 giờ', 'tone' => 'from-rose-200 via-white to-pink-50', 'image' => 'https://picsum.photos/seed/motorbike/800/600', 'badge' => 'Phổ biến'],
            ]);
        }

        // 3. Đánh giá khách hàng thật — lấy từ DB
        $reviewsFromDb = DanhGia::with(['khachHang', 'donDatLich.dichVu'])
            ->where('so_sao', '>=', 3)
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->an_danh ? 'Khách hàng ẩn danh' : $r->khachHang->ho_ten,
                'avatar' => $r->khachHang->anh_dai_dien ?? 'https://i.pravatar.cc/150?u=' . $r->khach_hang_id,
                'role' => 'Khách hàng',
                'rating' => $r->so_sao,
                'content' => $r->noi_dung ?? 'Dịch vụ rất tốt, tôi rất hài lòng!',
                'service' => $r->donDatLich?->dichVu?->ten_dich_vu ?? 'Dịch vụ',
            ]);

        // Fallback nếu chưa có đánh giá
        $customerReviews = $reviewsFromDb->isNotEmpty() ? $reviewsFromDb->values()->all() : [
            ['name' => 'Nguyễn Thị Mai', 'avatar' => 'https://i.pravatar.cc/150?img=1', 'role' => 'Khách du lịch', 'rating' => 5, 'content' => 'Đặt tour săn mây qua Dalat Services rất tiện lợi. Thợ đón đúng giờ, giá rõ ràng từ đầu.', 'service' => 'Tour Săn Mây Tà Xùa'],
            ['name' => 'Trần Văn Hoàng', 'avatar' => 'https://i.pravatar.cc/150?img=3', 'role' => 'Cư dân Đà Lạt', 'rating' => 5, 'content' => 'Gọi thợ sửa điều hòa qua app, 30 phút là có người tới. Giá hiển thị trước nên rất yên tâm.', 'service' => 'Sửa điều hòa tại nhà'],
            ['name' => 'Lê Phương Anh', 'avatar' => 'https://i.pravatar.cc/150?img=5', 'role' => 'Chủ homestay', 'rating' => 4, 'content' => 'Tôi dùng Dalat Services để đặt dịch vụ vệ sinh cho homestay mỗi tuần. Chất lượng ổn định.', 'service' => 'Vệ sinh nhà cửa'],
        ];

        // 4. Thống kê thực từ DB
        $avgRating = DanhGia::avg('so_sao');
        $realStats = [
            ['label' => 'Dịch vụ nổi bật', 'value' => DichVu::where('trang_thai_hoat_dong', 'hoat_dong')->count() . '+'],
            ['label' => 'Nhà cung cấp', 'value' => HoSoNhaCungCap::count() . '+'],
            ['label' => 'Đánh giá trung bình', 'value' => ($avgRating ? number_format($avgRating, 1) : '5.0') . '★'],
        ];

        return Inertia::render('Welcome', [
            'canRegister' => class_exists(Features::class) ? Features::enabled(Features::registration()) : true,
            'categories' => $categories,
            'featuredServices' => $featuredServices,
            'customerReviews' => $customerReviews,
            'stats' => $realStats,
        ]);
    }
}
