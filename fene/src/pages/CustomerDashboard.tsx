import { useState } from "react";
import { Link } from "react-router-dom";
import {
  Calendar,
  Clock,
  CheckCircle,
  XCircle,
  ChevronRight,
  User,
  Settings,
  LogOut,
} from "lucide-react";

export default function CustomerDashboard() {
  const [activeTab, setActiveTab] = useState("bookings");

  const bookings = [
    {
      id: "BK-1029",
      service: "Tour Săn Mây Tà Xùa 2N1Đ",
      provider: "Travel Pro",
      date: "15/10/2026",
      time: "06:00",
      status: "confirmed",
      price: 1500000,
      image: "https://picsum.photos/seed/taxua/200/200",
    },
    {
      id: "BK-1028",
      service: "Sửa Điện Nước Tại Nhà",
      provider: "Thợ Giỏi Hà Nội",
      date: "12/10/2026",
      time: "14:30",
      status: "completed",
      price: 250000,
      image: "https://picsum.photos/seed/repair/200/200",
    },
    {
      id: "BK-1027",
      service: "Thuê Xe Máy Đà Lạt",
      provider: "Đà Lạt Motor",
      date: "05/10/2026",
      time: "08:00",
      status: "cancelled",
      price: 120000,
      image: "https://picsum.photos/seed/motor/200/200",
    },
    {
      id: "BK-1026",
      service: "Vệ Sinh Máy Lạnh",
      provider: "Điện Lạnh 24h",
      date: "01/10/2026",
      time: "09:00",
      status: "pending",
      price: 200000,
      image: "https://picsum.photos/seed/ac/200/200",
    },
  ];

  const getStatusBadge = (status: string) => {
    switch (status) {
      case "confirmed":
        return (
          <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium flex items-center gap-1">
            <CheckCircle className="w-3 h-3" /> Đã xác nhận
          </span>
        );
      case "completed":
        return (
          <span className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium flex items-center gap-1">
            <CheckCircle className="w-3 h-3" /> Hoàn thành
          </span>
        );
      case "cancelled":
        return (
          <span className="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium flex items-center gap-1">
            <XCircle className="w-3 h-3" /> Đã hủy
          </span>
        );
      case "pending":
        return (
          <span className="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium flex items-center gap-1">
            <Clock className="w-3 h-3" /> Chờ xác nhận
          </span>
        );
      default:
        return null;
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="flex flex-col md:flex-row gap-8">
        {/* Sidebar */}
        <aside className="w-full md:w-64 shrink-0">
          <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-6 text-center border-b border-gray-100">
              <div className="w-20 h-20 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                <User className="w-10 h-10 text-indigo-600" />
              </div>
              <h2 className="text-lg font-bold text-gray-900">Nguyễn Văn A</h2>
              <p className="text-sm text-gray-500">Khách hàng</p>
            </div>
            <nav className="p-4 space-y-2">
              <button
                onClick={() => setActiveTab("bookings")}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${activeTab === "bookings" ? "bg-indigo-50 text-indigo-700" : "text-gray-600 hover:bg-gray-50"}`}
              >
                <Calendar className="w-5 h-5" /> Lịch sử đặt chỗ
              </button>
              <button
                onClick={() => setActiveTab("profile")}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${activeTab === "profile" ? "bg-indigo-50 text-indigo-700" : "text-gray-600 hover:bg-gray-50"}`}
              >
                <User className="w-5 h-5" /> Hồ sơ cá nhân
              </button>
              <button
                onClick={() => setActiveTab("settings")}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${activeTab === "settings" ? "bg-indigo-50 text-indigo-700" : "text-gray-600 hover:bg-gray-50"}`}
              >
                <Settings className="w-5 h-5" /> Cài đặt
              </button>
              <div className="border-t border-gray-100 my-2 pt-2"></div>
              <button className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                <LogOut className="w-5 h-5" /> Đăng xuất
              </button>
            </nav>
          </div>
        </aside>

        {/* Main Content */}
        <main className="flex-1">
          {activeTab === "bookings" && (
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                Lịch sử đặt chỗ
              </h2>

              <div className="space-y-4">
                {bookings.map((booking) => (
                  <div
                    key={booking.id}
                    className="flex flex-col sm:flex-row gap-4 p-4 rounded-xl border border-gray-100 hover:border-indigo-100 hover:shadow-sm transition-all group"
                  >
                    <img
                      src={booking.image}
                      alt={booking.service}
                      className="w-full sm:w-32 h-32 object-cover rounded-lg"
                      referrerPolicy="no-referrer"
                    />
                    <div className="flex-1 flex flex-col justify-between">
                      <div>
                        <div className="flex justify-between items-start mb-1">
                          <h3 className="font-bold text-gray-900 text-lg group-hover:text-indigo-600 transition-colors">
                            {booking.service}
                          </h3>
                          {getStatusBadge(booking.status)}
                        </div>
                        <p className="text-sm text-gray-500 mb-2">
                          Cung cấp bởi:{" "}
                          <span className="font-medium text-gray-700">
                            {booking.provider}
                          </span>
                        </p>
                        <div className="flex flex-wrap gap-4 text-sm text-gray-600">
                          <span className="flex items-center gap-1">
                            <Calendar className="w-4 h-4 text-gray-400" />{" "}
                            {booking.date}
                          </span>
                          <span className="flex items-center gap-1">
                            <Clock className="w-4 h-4 text-gray-400" />{" "}
                            {booking.time}
                          </span>
                          <span className="font-medium text-gray-900 ml-auto">
                            {new Intl.NumberFormat("vi-VN", {
                              style: "currency",
                              currency: "VND",
                            }).format(booking.price)}
                          </span>
                        </div>
                      </div>
                      <div className="flex justify-between items-center mt-4 pt-4 border-t border-gray-50">
                        <span className="text-xs text-gray-400 font-mono">
                          Mã ĐH: {booking.id}
                        </span>
                        <div className="flex gap-2">
                          {booking.status === "completed" && (
                            <button className="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                              Đánh giá
                            </button>
                          )}
                          <button className="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-1">
                            Chi tiết <ChevronRight className="w-4 h-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeTab !== "bookings" && (
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center h-96 text-gray-500">
              <Settings className="w-12 h-12 mb-4 text-gray-300" />
              <p>Tính năng đang được phát triển</p>
            </div>
          )}
        </main>
      </div>
    </div>
  );
}
