<?php

namespace Database\Seeders;

use App\Models\DanhMucDichVu;
use App\Models\DichVu;
use App\Models\HoSoNhaCungCap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = HoSoNhaCungCap::all();
        // Lấy danh sách các danh mục con (có parent_id != null)
        $subCategories = DanhMucDichVu::whereNotNull('parent_id')->get();

        if ($providers->isEmpty() || $subCategories->isEmpty()) {
            return;
        }

        $serviceSamples = [
            'Dọn nhà theo giờ' => [
                ['name' => 'Dọn dẹp căn hộ theo giờ', 'desc' => 'Nhân viên đến tận nơi dọn phòng khách, bếp, phòng ngủ và nhà vệ sinh. Phù hợp cho gia đình bận rộn hoặc căn hộ cần làm sạch định kỳ.', 'price' => [120000, 220000], 'unit' => 'Giờ', 'keywords' => ['dọn nhà', 'vệ sinh nhà', 'giúp việc theo giờ'], 'images' => ['https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80']],
                ['name' => 'Tổng vệ sinh nhà sau tiệc', 'desc' => 'Thu gom rác, lau sàn, khử mùi, làm sạch khu vực bếp và bàn ăn sau sinh nhật, liên hoan hoặc họp mặt gia đình.', 'price' => [350000, 750000], 'unit' => 'Gói', 'keywords' => ['tổng vệ sinh', 'dọn sau tiệc', 'lau dọn'], 'images' => ['https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Vệ sinh máy lạnh' => [
                ['name' => 'Vệ sinh máy lạnh treo tường', 'desc' => 'Tháo lọc bụi, rửa dàn lạnh, kiểm tra gas và vận hành thử. Dịch vụ giúp máy lạnh mát sâu hơn, tiết kiệm điện và giảm mùi ẩm.', 'price' => [150000, 300000], 'unit' => 'Máy', 'keywords' => ['máy lạnh', 'điều hòa', 'vệ sinh máy lạnh'], 'images' => ['https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Giặt sofa nệm' => [
                ['name' => 'Giặt sofa, nệm và rèm cửa tại nhà', 'desc' => 'Sử dụng máy hút giặt chuyên dụng, dung dịch an toàn cho trẻ nhỏ và thú cưng. Phù hợp cho căn hộ, homestay và văn phòng nhỏ.', 'price' => [250000, 900000], 'unit' => 'Gói', 'keywords' => ['giặt sofa', 'giặt nệm', 'vệ sinh rèm'], 'images' => ['https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Sửa điện nước' => [
                ['name' => 'Sửa điện nước gia đình khẩn cấp', 'desc' => 'Xử lý rò nước, nghẹt lavabo, chập điện, thay công tắc và ổ cắm. Thợ báo giá rõ trước khi làm, có mặt nhanh trong khu vực nội thành.', 'price' => [180000, 650000], 'unit' => 'Lượt', 'keywords' => ['sửa điện', 'sửa nước', 'thợ điện nước'], 'images' => ['https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Sơn nhà' => [
                ['name' => 'Sơn sửa phòng trọ và căn hộ', 'desc' => 'Tư vấn màu sơn, che chắn nội thất, xử lý bong tróc nhẹ và sơn hoàn thiện. Phù hợp khi chuyển nhà, trả phòng hoặc làm mới không gian.', 'price' => [450000, 2500000], 'unit' => 'Gói', 'keywords' => ['sơn nhà', 'sơn phòng', 'thợ sơn'], 'images' => ['https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Chống thấm' => [
                ['name' => 'Chống thấm sân thượng và nhà vệ sinh', 'desc' => 'Khảo sát điểm thấm, xử lý khe nứt, phủ vật liệu chống thấm và bảo hành sau thi công. Thích hợp cho nhà phố mùa mưa.', 'price' => [800000, 4500000], 'unit' => 'Gói', 'keywords' => ['chống thấm', 'sân thượng', 'nhà vệ sinh'], 'images' => ['https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Thợ mộc' => [
                ['name' => 'Sửa tủ bếp, bàn ghế và cửa gỗ', 'desc' => 'Căn chỉnh bản lề, thay ray kéo, xử lý kẹt cửa và gia cố bàn ghế gỗ. Có nhận đóng kệ nhỏ theo kích thước thực tế.', 'price' => [250000, 1500000], 'unit' => 'Lượt', 'keywords' => ['thợ mộc', 'sửa tủ', 'sửa cửa gỗ'], 'images' => ['https://images.unsplash.com/photo-1601058268499-e52658b8bb88?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Thuê xe máy' => [
                ['name' => 'Thuê xe máy giao tận nơi', 'desc' => 'Xe số, xe ga được bảo dưỡng định kỳ, kèm mũ bảo hiểm và áo mưa. Giao xe tại khách sạn, homestay hoặc bến xe trong nội thành.', 'price' => [120000, 220000], 'unit' => 'Ngày', 'keywords' => ['thuê xe máy', 'giao xe', 'du lịch'], 'images' => ['https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Thuê ô tô' => [
                ['name' => 'Thuê ô tô 4-7 chỗ có tài xế', 'desc' => 'Phục vụ đi sân bay, tham quan ngoại ô, công tác hoặc đưa đón gia đình. Tài xế rành đường, xe sạch và báo giá theo lịch trình.', 'price' => [700000, 2200000], 'unit' => 'Chuyến', 'keywords' => ['thuê ô tô', 'xe có tài xế', 'đưa đón'], 'images' => ['https://images.unsplash.com/photo-1549924231-f129b911e442?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Hướng dẫn viên' => [
                ['name' => 'Hướng dẫn viên địa phương nửa ngày', 'desc' => 'Gợi ý điểm ăn chơi, kể chuyện văn hóa địa phương và hỗ trợ chụp ảnh. Phù hợp nhóm bạn, gia đình hoặc khách lần đầu đến thành phố.', 'price' => [450000, 1000000], 'unit' => 'Buổi', 'keywords' => ['hướng dẫn viên', 'tour địa phương', 'tham quan'], 'images' => ['https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Đưa đón sân bay' => [
                ['name' => 'Đưa đón sân bay đúng giờ', 'desc' => 'Theo dõi giờ bay, hỗ trợ hành lý và đưa đón tận khách sạn hoặc nhà riêng. Có xe 4 chỗ, 7 chỗ và báo giá trọn gói.', 'price' => [300000, 850000], 'unit' => 'Chuyến', 'keywords' => ['sân bay', 'đưa đón', 'taxi'], 'images' => ['https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Spa tại nhà' => [
                ['name' => 'Massage thư giãn tại nhà', 'desc' => 'Kỹ thuật viên mang theo giường gấp, khăn sạch và tinh dầu. Dịch vụ phù hợp người làm việc căng thẳng hoặc khách lưu trú tại homestay.', 'price' => [250000, 650000], 'unit' => 'Buổi', 'keywords' => ['massage', 'spa tại nhà', 'thư giãn'], 'images' => ['https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Cắt tóc' => [
                ['name' => 'Cắt tóc nam nữ tại nhà', 'desc' => 'Thợ mang dụng cụ sạch, tư vấn kiểu tóc phù hợp khuôn mặt và dọn tóc sau khi làm. Phù hợp người bận rộn hoặc gia đình có trẻ nhỏ.', 'price' => [90000, 300000], 'unit' => 'Lượt', 'keywords' => ['cắt tóc', 'làm tóc tại nhà', 'tạo kiểu'], 'images' => ['https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Trang điểm tiệc' => [
                ['name' => 'Trang điểm dự tiệc và chụp ảnh', 'desc' => 'Makeup tự nhiên, bền nền, kèm làm tóc nhẹ cho tiệc cưới, sinh nhật hoặc chụp kỷ yếu. Có nhận lịch sáng sớm.', 'price' => [350000, 1200000], 'unit' => 'Lượt', 'keywords' => ['trang điểm', 'makeup', 'làm tóc'], 'images' => ['https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Bác sĩ thú y' => [
                ['name' => 'Khám thú cưng tại nhà', 'desc' => 'Kiểm tra sức khỏe chó mèo, tư vấn tiêm phòng, xử lý bệnh nhẹ và hướng dẫn chăm sóc sau khám. Có hỗ trợ ngoài giờ theo lịch hẹn.', 'price' => [200000, 700000], 'unit' => 'Lượt', 'keywords' => ['thú y', 'chó mèo', 'khám tại nhà'], 'images' => ['https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Chụp ảnh' => [
                ['name' => 'Chụp ảnh gia đình và du lịch', 'desc' => 'Nhiếp ảnh gia gợi ý góc chụp, chỉnh màu cơ bản và bàn giao ảnh online. Phù hợp chụp couple, gia đình hoặc nhóm bạn.', 'price' => [600000, 1800000], 'unit' => 'Gói', 'keywords' => ['chụp ảnh', 'nhiếp ảnh', 'ảnh gia đình'], 'images' => ['https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Trang trí tiệc' => [
                ['name' => 'Trang trí sinh nhật và thôi nôi', 'desc' => 'Thiết kế backdrop, bong bóng, bàn gallery và phụ kiện theo màu chủ đề. Đội ngũ lắp đặt, tháo dọn sau tiệc.', 'price' => [900000, 3500000], 'unit' => 'Gói', 'keywords' => ['trang trí tiệc', 'sinh nhật', 'thôi nôi'], 'images' => ['https://images.unsplash.com/photo-1530103862676-de8c9debad1d?auto=format&fit=crop&w=1200&q=80']],
            ],
            'MC Sự kiện' => [
                ['name' => 'MC tiệc cưới, khai trương, tất niên', 'desc' => 'Dẫn chương trình song ngữ cơ bản, khuấy động không khí và phối hợp kịch bản với ban tổ chức. Có hỗ trợ chuẩn bị lời dẫn.', 'price' => [1200000, 4500000], 'unit' => 'Buổi', 'keywords' => ['mc sự kiện', 'tiệc cưới', 'khai trương'], 'images' => ['https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=1200&q=80']],
            ],
            'Thuê lều trại' => [
                ['name' => 'Thuê lều trại picnic cuối tuần', 'desc' => 'Cho thuê lều, bàn ghế gấp, đèn pin và bếp nướng nhỏ. Có gói giao nhận tận nơi cho nhóm đi cắm trại gần thành phố.', 'price' => [180000, 900000], 'unit' => 'Gói', 'keywords' => ['thuê lều', 'cắm trại', 'picnic'], 'images' => ['https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1200&q=80']],
            ],
        ];

        // Tạo dịch vụ mẫu bằng tiếng Việt, bám sát các nhu cầu phổ biến ở Việt Nam.
        foreach ($providers as $provider) {
            // Mỗi nhà cung cấp có từ 2 đến 4 dịch vụ để dữ liệu demo đa dạng nhưng không bị rối.
            $soDichVuTrongKho = rand(2, 4);

            for ($i = 0; $i < $soDichVuTrongKho; $i++) {
                $category = $subCategories->random();
                $samples = $serviceSamples[$category->ten_danh_muc] ?? [];
                $fallbackSample = [
                    'name' => $category->ten_danh_muc . ' tận nơi',
                    'desc' => 'Dịch vụ địa phương được cung cấp tận nơi, báo giá minh bạch và phù hợp nhu cầu sinh hoạt hằng ngày của gia đình Việt.',
                    'price' => [150000, 600000],
                    'unit' => 'Lượt',
                    'keywords' => [$category->ten_danh_muc, 'dịch vụ tận nơi'],
                    'images' => ['https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80'],
                ];
                $sample = empty($samples) ? $fallbackSample : $samples[array_rand($samples)];
                $slug = Str::slug($sample['name']) . '-' . Str::lower(Str::random(5));

                DichVu::factory()->create([
                    'nha_cung_cap_id' => $provider->id,
                    'danh_muc_id' => $category->id,
                    'ten_dich_vu' => $sample['name'],
                    'slug' => $slug,
                    'mo_ta_chi_tiet' => $sample['desc'],
                    'gia_tu' => $sample['price'][0],
                    'gia_den' => $sample['price'][1],
                    'don_vi_gia' => $sample['unit'],
                    'dia_chi_hien_thi' => fake()->randomElement(['Quận 1, TP. Hồ Chí Minh', 'Cầu Giấy, Hà Nội', 'Hải Châu, Đà Nẵng', 'Nha Trang, Khánh Hòa', 'Trung tâm Đà Lạt']),
                    'danh_sach_anh' => $sample['images'],
                    'the_tu_khoa' => $sample['keywords'],
                ]);
            }
        }
    }
}
