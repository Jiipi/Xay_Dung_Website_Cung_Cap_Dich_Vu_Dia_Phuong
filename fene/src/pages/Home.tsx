import { Link } from "react-router-dom";
import { Search, MapPin, Star, Wrench, Car, Map, Sparkles } from "lucide-react";

export default function Home() {
  const categories = [
    {
      id: "du_lich",
      name: "Du lịch & Tour",
      icon: <Map className="w-6 h-6" />,
      color: "bg-blue-100 text-blue-600",
    },
    {
      id: "sua_chua",
      name: "Sửa chữa",
      icon: <Wrench className="w-6 h-6" />,
      color: "bg-orange-100 text-orange-600",
    },
    {
      id: "thue_xe",
      name: "Thuê xe",
      icon: <Car className="w-6 h-6" />,
      color: "bg-green-100 text-green-600",
    },
  ];

  const featuredServices = [
    {
      id: 1,
      title: "Tour Săn Mây Tà Xùa 2N1Đ",
      provider: "Travel Pro",
      rating: 4.9,
      reviews: 128,
      price: "1.500.000đ",
      image: "https://picsum.photos/seed/taxua/600/400",
    },
    {
      id: 2,
      title: "Sửa Điện Nước Tại Nhà 24/7",
      provider: "Thợ Giỏi Hà Nội",
      rating: 4.8,
      reviews: 342,
      price: "Từ 150.000đ",
      image: "https://picsum.photos/seed/repair/600/400",
    },
    {
      id: 3,
      title: "Thuê Xe Máy Đà Lạt Giao Tận Nơi",
      provider: "Đà Lạt Motor",
      rating: 4.7,
      reviews: 89,
      price: "120.000đ/ngày",
      image: "https://picsum.photos/seed/motor/600/400",
    },
    {
      id: 4,
      title: "Vệ Sinh Máy Lạnh Chuyên Nghiệp",
      provider: "Điện Lạnh 24h",
      rating: 4.9,
      reviews: 512,
      price: "200.000đ",
      image: "https://picsum.photos/seed/ac/600/400",
    },
  ];

  return (
    <div className="flex flex-col">
      {/* Hero Section */}
      <section className="relative bg-indigo-900 text-white overflow-hidden">
        <div className="absolute inset-0 opacity-20">
          <img
            src="https://picsum.photos/seed/hero/1920/1080"
            alt="Hero Background"
            className="w-full h-full object-cover"
            referrerPolicy="no-referrer"
          />
        </div>
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 flex flex-col items-center text-center">
          <h1 className="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">
            Tìm dịch vụ & Trải nghiệm <br className="hidden md:block" />
            <span className="text-indigo-300">Hoàn hảo cùng AI</span>
          </h1>
          <p className="text-xl md:text-2xl text-indigo-100 mb-10 max-w-3xl">
            Từ sửa chữa nhà cửa đến lên lịch trình du lịch, mọi thứ đều dễ dàng
            hơn.
          </p>

          {/* Search Bar */}
          <div className="w-full max-w-4xl bg-white rounded-full p-2 flex flex-col md:flex-row shadow-2xl">
            <div className="flex-1 flex items-center px-4 py-3 md:py-0 border-b md:border-b-0 md:border-r border-gray-200">
              <Search className="w-5 h-5 text-gray-400 mr-3" />
              <input
                type="text"
                placeholder="Bạn đang tìm dịch vụ gì?"
                className="w-full bg-transparent text-gray-900 focus:outline-none placeholder-gray-500"
              />
            </div>
            <div className="flex-1 flex items-center px-4 py-3 md:py-0">
              <MapPin className="w-5 h-5 text-gray-400 mr-3" />
              <input
                type="text"
                placeholder="Khu vực (VD: Hà Nội, Quận 1...)"
                className="w-full bg-transparent text-gray-900 focus:outline-none placeholder-gray-500"
              />
            </div>
            <button className="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full px-8 py-3 md:py-4 font-semibold transition-colors mt-2 md:mt-0 w-full md:w-auto">
              Tìm kiếm
            </button>
          </div>
        </div>
      </section>

      {/* Categories */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-8">
            Danh mục nổi bật
          </h2>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {categories.map((cat) => (
              <Link
                key={cat.id}
                to={`/services?category=${cat.id}`}
                className="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors border border-gray-100"
              >
                <div
                  className={`w-16 h-16 rounded-full flex items-center justify-center mb-4 ${cat.color}`}
                >
                  {cat.icon}
                </div>
                <span className="font-medium text-gray-900 text-center">
                  {cat.name}
                </span>
              </Link>
            ))}
            <Link
              to="/ai-planner"
              className="flex flex-col items-center p-6 bg-indigo-50 rounded-2xl hover:bg-indigo-100 transition-colors border border-indigo-100 group"
            >
              <div className="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-indigo-200 text-indigo-700 group-hover:scale-110 transition-transform">
                <Sparkles className="w-6 h-6" />
              </div>
              <span className="font-medium text-indigo-900 text-center">
                AI Lên Lịch Trình
              </span>
            </Link>
          </div>
        </div>
      </section>

      {/* AI Recommended / High Rated */}
      <section className="py-16 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-end mb-8">
            <div>
              <h2 className="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <Sparkles className="w-6 h-6 text-indigo-600" /> Gợi ý cho bạn
              </h2>
              <p className="text-gray-500 mt-1">
                Dịch vụ được đánh giá cao nhất
              </p>
            </div>
            <Link
              to="/services"
              className="text-indigo-600 hover:text-indigo-800 font-medium hidden md:block"
            >
              Xem tất cả
            </Link>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {featuredServices.map((service) => (
              <Link
                key={service.id}
                to={`/services/${service.id}`}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 flex flex-col"
              >
                <div className="relative h-48">
                  <img
                    src={service.image}
                    alt={service.title}
                    className="w-full h-full object-cover"
                    referrerPolicy="no-referrer"
                  />
                  <div className="absolute top-3 right-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg flex items-center gap-1 text-sm font-bold text-gray-900">
                    <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                    {service.rating}
                  </div>
                </div>
                <div className="p-5 flex flex-col flex-1">
                  <div className="text-sm text-gray-500 mb-1">
                    {service.provider}
                  </div>
                  <h3 className="font-bold text-gray-900 mb-2 line-clamp-2">
                    {service.title}
                  </h3>
                  <div className="mt-auto flex justify-between items-center pt-4 border-t border-gray-100">
                    <span className="font-bold text-indigo-600">
                      {service.price}
                    </span>
                    <span className="text-xs text-gray-500">
                      ({service.reviews})
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
