import { useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { Filter, Star, MapPin, Search } from "lucide-react";

export default function ServiceListing() {
  const [searchParams] = useSearchParams();
  const category = searchParams.get("category") || "all";

  const [priceRange, setPriceRange] = useState([0, 5000000]);
  const [ratingFilter, setRatingFilter] = useState(0);

  const services = [
    {
      id: 1,
      title: "Tour Săn Mây Tà Xùa 2N1Đ",
      provider: "Travel Pro",
      rating: 4.9,
      reviews: 128,
      price: 1500000,
      category: "du_lich",
      location: "Sơn La",
      image: "https://picsum.photos/seed/taxua/600/400",
    },
    {
      id: 2,
      title: "Sửa Điện Nước Tại Nhà 24/7",
      provider: "Thợ Giỏi Hà Nội",
      rating: 4.8,
      reviews: 342,
      price: 150000,
      category: "sua_chua",
      location: "Hà Nội",
      image: "https://picsum.photos/seed/repair/600/400",
    },
    {
      id: 3,
      title: "Thuê Xe Máy Đà Lạt Giao Tận Nơi",
      provider: "Đà Lạt Motor",
      rating: 4.7,
      reviews: 89,
      price: 120000,
      category: "thue_xe",
      location: "Đà Lạt",
      image: "https://picsum.photos/seed/motor/600/400",
    },
    {
      id: 4,
      title: "Vệ Sinh Máy Lạnh Chuyên Nghiệp",
      provider: "Điện Lạnh 24h",
      rating: 4.9,
      reviews: 512,
      price: 200000,
      category: "sua_chua",
      location: "TP.HCM",
      image: "https://picsum.photos/seed/ac/600/400",
    },
    {
      id: 5,
      title: "Tour Khám Phá Hang Sơn Đoòng",
      provider: "Oxalis Adventure",
      rating: 5.0,
      reviews: 1024,
      price: 69000000,
      category: "du_lich",
      location: "Quảng Bình",
      image: "https://picsum.photos/seed/cave/600/400",
    },
    {
      id: 6,
      title: "Sửa Chữa Tủ Lạnh Tại Nhà",
      provider: "Điện Máy Xanh",
      rating: 4.5,
      reviews: 210,
      price: 300000,
      category: "sua_chua",
      location: "Đà Nẵng",
      image: "https://picsum.photos/seed/fridge/600/400",
    },
  ];

  const filteredServices = services.filter(
    (s) =>
      (category === "all" || s.category === category) &&
      s.price >= priceRange[0] &&
      s.price <= priceRange[1] &&
      s.rating >= ratingFilter,
  );

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="flex flex-col md:flex-row gap-8">
        {/* Sidebar Filters */}
        <aside className="w-full md:w-64 shrink-0">
          <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
            <h2 className="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
              <Filter className="w-5 h-5" /> Bộ lọc
            </h2>

            {/* Category Filter */}
            <div className="mb-6">
              <h3 className="font-semibold text-gray-900 mb-3">Danh mục</h3>
              <div className="space-y-2">
                {["all", "du_lich", "sua_chua", "thue_xe"].map((cat) => (
                  <label
                    key={cat}
                    className="flex items-center gap-2 cursor-pointer"
                  >
                    <input
                      type="radio"
                      name="category"
                      checked={category === cat}
                      onChange={() => {}}
                      className="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span className="text-gray-700 capitalize">
                      {cat.replace("_", " ")}
                    </span>
                  </label>
                ))}
              </div>
            </div>

            {/* Price Filter */}
            <div className="mb-6">
              <h3 className="font-semibold text-gray-900 mb-3">Mức giá</h3>
              <div className="space-y-4">
                <input
                  type="range"
                  min="0"
                  max="10000000"
                  step="100000"
                  value={priceRange[1]}
                  onChange={(e) => setPriceRange([0, parseInt(e.target.value)])}
                  className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                />
                <div className="flex justify-between text-sm text-gray-500">
                  <span>0đ</span>
                  <span>
                    {new Intl.NumberFormat("vi-VN", {
                      style: "currency",
                      currency: "VND",
                    }).format(priceRange[1])}
                  </span>
                </div>
              </div>
            </div>

            {/* Rating Filter */}
            <div>
              <h3 className="font-semibold text-gray-900 mb-3">Đánh giá</h3>
              <div className="space-y-2">
                {[4, 3, 0].map((rating) => (
                  <label
                    key={rating}
                    className="flex items-center gap-2 cursor-pointer"
                  >
                    <input
                      type="radio"
                      name="rating"
                      checked={ratingFilter === rating}
                      onChange={() => setRatingFilter(rating)}
                      className="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span className="flex items-center text-gray-700">
                      {rating === 0 ? (
                        "Tất cả"
                      ) : (
                        <>
                          Từ {rating}{" "}
                          <Star className="w-4 h-4 fill-yellow-400 text-yellow-400 ml-1" />
                        </>
                      )}
                    </span>
                  </label>
                ))}
              </div>
            </div>
          </div>
        </aside>

        {/* Main Content */}
        <main className="flex-1">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-2xl font-bold text-gray-900">
              {category === "all"
                ? "Tất cả dịch vụ"
                : `Dịch vụ ${category.replace("_", " ")}`}
            </h1>
            <span className="text-gray-500">
              {filteredServices.length} kết quả
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredServices.map((service) => (
              <Link
                key={service.id}
                to={`/services/${service.id}`}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 flex flex-col group"
              >
                <div className="relative h-48 overflow-hidden">
                  <img
                    src={service.image}
                    alt={service.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    referrerPolicy="no-referrer"
                  />
                  <div className="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg flex items-center gap-1 text-sm font-bold text-gray-900">
                    <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                    {service.rating}
                  </div>
                </div>
                <div className="p-5 flex flex-col flex-1">
                  <div className="flex items-center text-sm text-gray-500 mb-2 gap-1">
                    <MapPin className="w-4 h-4" /> {service.location}
                  </div>
                  <h3 className="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                    {service.title}
                  </h3>
                  <div className="text-sm text-gray-500 mb-4">
                    {service.provider}
                  </div>
                  <div className="mt-auto flex justify-between items-center pt-4 border-t border-gray-100">
                    <span className="font-bold text-indigo-600 text-lg">
                      {new Intl.NumberFormat("vi-VN", {
                        style: "currency",
                        currency: "VND",
                      }).format(service.price)}
                    </span>
                    <span className="text-xs text-gray-500">
                      ({service.reviews} đánh giá)
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>

          {filteredServices.length === 0 && (
            <div className="text-center py-20 bg-white rounded-2xl border border-gray-100">
              <Search className="w-12 h-12 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900">
                Không tìm thấy dịch vụ nào
              </h3>
              <p className="text-gray-500 mt-2">
                Vui lòng thử điều chỉnh bộ lọc của bạn.
              </p>
            </div>
          )}
        </main>
      </div>
    </div>
  );
}
