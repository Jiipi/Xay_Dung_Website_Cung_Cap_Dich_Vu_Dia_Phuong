import { useParams, Link } from "react-router-dom";
import {
  Star,
  MapPin,
  CheckCircle,
  Clock,
  Shield,
  User,
  MessageSquare,
  Calendar,
} from "lucide-react";

export default function ServiceDetail() {
  const { id } = useParams();

  // Mock data based on ID (normally fetch from API)
  const service = {
    id: 1,
    title: "Tour Săn Mây Tà Xùa 2N1Đ - Khám Phá Vẻ Đẹp Tây Bắc",
    provider: {
      name: "Travel Pro",
      rating: 4.9,
      reviews: 128,
      verified: true,
      experience: "5 năm",
      avatar: "https://picsum.photos/seed/avatar1/100/100",
    },
    rating: 4.9,
    reviews: 128,
    price: 1500000,
    location: "Bắc Yên, Sơn La",
    images: [
      "https://picsum.photos/seed/taxua1/1200/800",
      "https://picsum.photos/seed/taxua2/600/400",
      "https://picsum.photos/seed/taxua3/600/400",
      "https://picsum.photos/seed/taxua4/600/400",
    ],
    description:
      "Khám phá Tà Xùa - thiên đường mây của Tây Bắc. Hành trình 2 ngày 1 đêm sẽ đưa bạn đến những điểm ngắm mây đẹp nhất, trải nghiệm văn hóa bản địa và thưởng thức đặc sản vùng cao. Tour bao gồm xe đưa đón, chỗ ở homestay view mây, các bữa ăn và hướng dẫn viên nhiệt tình.",
    attributes: [
      { name: "Thời gian", value: "2 Ngày 1 Đêm" },
      { name: "Phương tiện", value: "Xe giường nằm" },
      { name: "Khởi hành", value: "Hàng ngày" },
      { name: "Số người tối đa", value: "15 người/đoàn" },
    ],
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      {/* Breadcrumbs */}
      <nav className="flex text-sm text-gray-500 mb-6">
        <Link to="/" className="hover:text-indigo-600">
          Trang chủ
        </Link>
        <span className="mx-2">/</span>
        <Link to="/services?category=du_lich" className="hover:text-indigo-600">
          Du lịch
        </Link>
        <span className="mx-2">/</span>
        <span className="text-gray-900 font-medium truncate">
          {service.title}
        </span>
      </nav>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Main Content */}
        <div className="flex-1">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            {service.title}
          </h1>
          <div className="flex items-center gap-4 text-sm text-gray-600 mb-6">
            <div className="flex items-center gap-1">
              <Star className="w-5 h-5 fill-yellow-400 text-yellow-400" />
              <span className="font-bold text-gray-900">{service.rating}</span>
              <span className="underline cursor-pointer">
                ({service.reviews} đánh giá)
              </span>
            </div>
            <div className="flex items-center gap-1">
              <MapPin className="w-5 h-5 text-gray-400" />
              {service.location}
            </div>
          </div>

          {/* Image Gallery */}
          <div className="grid grid-cols-4 gap-2 mb-8 rounded-2xl overflow-hidden">
            <div className="col-span-4 md:col-span-2 row-span-2 h-64 md:h-96">
              <img
                src={service.images[0]}
                alt="Main"
                className="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                referrerPolicy="no-referrer"
              />
            </div>
            {service.images.slice(1).map((img, idx) => (
              <div key={idx} className="col-span-2 md:col-span-1 h-32 md:h-48">
                <img
                  src={img}
                  alt={`Gallery ${idx}`}
                  className="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                  referrerPolicy="no-referrer"
                />
              </div>
            ))}
          </div>

          {/* Description */}
          <section className="mb-10">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">
              Mô tả dịch vụ
            </h2>
            <p className="text-gray-700 leading-relaxed whitespace-pre-line">
              {service.description}
            </p>
          </section>

          {/* Attributes */}
          <section className="mb-10 bg-gray-50 p-6 rounded-2xl border border-gray-100">
            <h2 className="text-xl font-bold text-gray-900 mb-4">
              Thông tin chi tiết
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {service.attributes.map((attr, idx) => (
                <div key={idx} className="flex items-center gap-3">
                  <CheckCircle className="w-5 h-5 text-indigo-600" />
                  <div>
                    <span className="text-gray-500 text-sm block">
                      {attr.name}
                    </span>
                    <span className="font-medium text-gray-900">
                      {attr.value}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </section>

          {/* Provider Info */}
          <section className="mb-10 border-t border-gray-200 pt-10">
            <h2 className="text-2xl font-bold text-gray-900 mb-6">
              Thông tin nhà cung cấp
            </h2>
            <div className="flex items-start gap-6">
              <img
                src={service.provider.avatar}
                alt={service.provider.name}
                className="w-16 h-16 rounded-full object-cover border border-gray-200"
                referrerPolicy="no-referrer"
              />
              <div>
                <h3 className="text-xl font-bold text-gray-900 flex items-center gap-2">
                  {service.provider.name}
                  {service.provider.verified && (
                    <Shield className="w-5 h-5 text-blue-500 fill-blue-50" />
                  )}
                </h3>
                <div className="flex items-center gap-4 mt-2 text-sm text-gray-600">
                  <span className="flex items-center gap-1">
                    <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />{" "}
                    {service.provider.rating} ({service.provider.reviews})
                  </span>
                  <span className="flex items-center gap-1">
                    <Clock className="w-4 h-4 text-gray-400" /> Kinh nghiệm:{" "}
                    {service.provider.experience}
                  </span>
                </div>
                <button className="mt-4 px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg font-medium hover:bg-indigo-50 transition-colors flex items-center gap-2">
                  <MessageSquare className="w-4 h-4" /> Liên hệ nhà cung cấp
                </button>
              </div>
            </div>
          </section>
        </div>

        {/* Booking Sidebar */}
        <aside className="w-full lg:w-96 shrink-0">
          <div className="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 sticky top-24">
            <div className="mb-6">
              <span className="text-3xl font-bold text-indigo-600">
                {new Intl.NumberFormat("vi-VN", {
                  style: "currency",
                  currency: "VND",
                }).format(service.price)}
              </span>
              <span className="text-gray-500 text-sm"> / người</span>
            </div>

            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Ngày bắt đầu
                </label>
                <div className="relative">
                  <Calendar className="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                  <input
                    type="date"
                    className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Số lượng khách
                </label>
                <div className="relative">
                  <User className="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                  <select className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all appearance-none">
                    {[1, 2, 3, 4, 5].map((n) => (
                      <option key={n} value={n}>
                        {n} người
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="pt-4 border-t border-gray-100">
                <div className="flex justify-between mb-2 text-gray-600">
                  <span>Tạm tính</span>
                  <span>
                    {new Intl.NumberFormat("vi-VN", {
                      style: "currency",
                      currency: "VND",
                    }).format(service.price)}
                  </span>
                </div>
                <div className="flex justify-between mb-4 text-gray-600">
                  <span>Phí dịch vụ</span>
                  <span>Miễn phí</span>
                </div>
                <div className="flex justify-between font-bold text-lg text-gray-900">
                  <span>Tổng tiền</span>
                  <span>
                    {new Intl.NumberFormat("vi-VN", {
                      style: "currency",
                      currency: "VND",
                    }).format(service.price)}
                  </span>
                </div>
              </div>

              <button
                type="button"
                className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]"
              >
                Đặt lịch ngay
              </button>

              <p className="text-center text-xs text-gray-500 mt-4">
                Bạn sẽ không bị trừ tiền cho đến khi nhà cung cấp xác nhận.
              </p>
            </form>
          </div>
        </aside>
      </div>
    </div>
  );
}
