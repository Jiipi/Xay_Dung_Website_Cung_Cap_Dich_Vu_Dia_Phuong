import { useState } from "react";
import { Link } from "react-router-dom";
import {
  CheckCircle,
  XCircle,
  Search,
  Filter,
  Calendar,
  Clock,
  MapPin,
  User,
  BarChart3,
  Briefcase,
  CalendarCheck,
  Settings,
  LogOut,
  Star,
} from "lucide-react";

export default function ManageBookings() {
  const [bookings, setBookings] = useState([
    {
      id: "BK-1030",
      customer: "Trần Thị B",
      phone: "0987654321",
      service: "Vệ sinh máy lạnh",
      date: "16/10/2026",
      time: "09:00",
      address: "Quận 1, TP.HCM",
      status: "pending",
      amount: 200000,
      note: "Nhà có trẻ nhỏ, thợ đến nhẹ nhàng",
    },
    {
      id: "BK-1029",
      customer: "Nguyễn Văn A",
      phone: "0912345678",
      service: "Sửa điện nước",
      date: "15/10/2026",
      time: "14:30",
      address: "Bình Thạnh, TP.HCM",
      status: "confirmed",
      amount: 150000,
      note: "",
    },
    {
      id: "BK-1028",
      customer: "Lê Văn C",
      phone: "0909090909",
      service: "Sửa tủ lạnh",
      date: "14/10/2026",
      time: "10:00",
      address: "Quận 3, TP.HCM",
      status: "completed",
      amount: 350000,
      note: "Tủ lạnh không đông đá",
    },
  ]);

  const handleStatusChange = (id: string, newStatus: string) => {
    setBookings(
      bookings.map((b) => (b.id === id ? { ...b, status: newStatus } : b)),
    );
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="flex flex-col md:flex-row gap-8">
        {/* Sidebar */}
        <aside className="w-full md:w-64 shrink-0">
          <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
            <div className="p-6 text-center border-b border-gray-100">
              <div className="w-20 h-20 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4 overflow-hidden">
                <img
                  src="https://picsum.photos/seed/provider/100/100"
                  alt="Provider"
                  className="w-full h-full object-cover"
                  referrerPolicy="no-referrer"
                />
              </div>
              <h2 className="text-lg font-bold text-gray-900">Điện Lạnh 24h</h2>
              <p className="text-sm text-gray-500 flex items-center justify-center gap-1 mt-1">
                <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" /> 4.9
                (512 đánh giá)
              </p>
            </div>
            <nav className="p-4 space-y-2">
              <Link
                to="/provider/dashboard"
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
              >
                <BarChart3 className="w-5 h-5" /> Tổng quan
              </Link>
              <Link
                to="/provider/services"
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
              >
                <Briefcase className="w-5 h-5" /> Quản lý dịch vụ
              </Link>
              <Link
                to="/provider/bookings"
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700 transition-colors"
              >
                <CalendarCheck className="w-5 h-5" /> Quản lý Booking
              </Link>
              <button className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                <Settings className="w-5 h-5" /> Cài đặt cửa hàng
              </button>
              <div className="border-t border-gray-100 my-2 pt-2"></div>
              <button className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                <LogOut className="w-5 h-5" /> Đăng xuất
              </button>
            </nav>
          </div>
        </aside>

        {/* Main Content */}
        <main className="flex-1 space-y-6">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 className="text-2xl font-bold text-gray-900">
              Quản lý Booking
            </h1>
            <div className="flex gap-2">
              <div className="relative">
                <Search className="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  placeholder="Tìm mã ĐH, khách hàng..."
                  className="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-sm w-full sm:w-64 shadow-sm"
                />
              </div>
              <button
                className="p-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors shadow-sm"
                title="Lọc"
              >
                <Filter className="w-5 h-5" />
              </button>
            </div>
          </div>

          <div className="space-y-4">
            {bookings.map((booking) => (
              <div
                key={booking.id}
                className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:border-indigo-100 transition-colors"
              >
                <div
                  className={`p-4 border-b ${booking.status === "pending" ? "bg-yellow-50 border-yellow-100" : booking.status === "confirmed" ? "bg-blue-50 border-blue-100" : "bg-gray-50 border-gray-100"} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2`}
                >
                  <div className="flex items-center gap-3">
                    <span className="font-bold text-gray-900 text-lg">
                      {booking.id}
                    </span>
                    {booking.status === "pending" && (
                      <span className="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium border border-yellow-200">
                        Chờ xác nhận
                      </span>
                    )}
                    {booking.status === "confirmed" && (
                      <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs font-medium border border-blue-200">
                        Đã xác nhận
                      </span>
                    )}
                    {booking.status === "completed" && (
                      <span className="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium border border-green-200">
                        Hoàn thành
                      </span>
                    )}
                    {booking.status === "cancelled" && (
                      <span className="px-2 py-1 bg-red-100 text-red-800 rounded-md text-xs font-medium border border-red-200">
                        Đã hủy
                      </span>
                    )}
                  </div>
                  <div className="text-sm text-gray-500 font-medium">
                    Ngày đặt: {booking.date}
                  </div>
                </div>

                <div className="p-6 flex flex-col md:flex-row gap-6">
                  <div className="flex-1 space-y-4">
                    <div>
                      <h3 className="text-lg font-bold text-indigo-900 mb-1">
                        {booking.service}
                      </h3>
                      <p className="text-sm text-gray-600 flex items-center gap-2">
                        <Calendar className="w-4 h-4" /> {booking.date}{" "}
                        <span className="mx-1">|</span>{" "}
                        <Clock className="w-4 h-4" /> {booking.time}
                      </p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                      <div>
                        <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                          Khách hàng
                        </p>
                        <p className="font-medium text-gray-900 flex items-center gap-2">
                          <User className="w-4 h-4 text-gray-400" />{" "}
                          {booking.customer}
                        </p>
                        <p className="text-sm text-gray-600 mt-1">
                          {booking.phone}
                        </p>
                      </div>
                      <div>
                        <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                          Địa điểm
                        </p>
                        <p className="font-medium text-gray-900 flex items-start gap-2">
                          <MapPin className="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />{" "}
                          {booking.address}
                        </p>
                      </div>
                    </div>

                    {booking.note && (
                      <div className="bg-yellow-50 p-3 rounded-lg border border-yellow-100 text-sm text-yellow-800">
                        <span className="font-semibold">Ghi chú:</span>{" "}
                        {booking.note}
                      </div>
                    )}
                  </div>

                  <div className="w-full md:w-48 shrink-0 flex flex-col justify-between border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                    <div>
                      <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        Tổng tiền
                      </p>
                      <p className="text-2xl font-bold text-indigo-600">
                        {new Intl.NumberFormat("vi-VN", {
                          style: "currency",
                          currency: "VND",
                        }).format(booking.amount)}
                      </p>
                    </div>

                    <div className="mt-6 space-y-2">
                      {booking.status === "pending" && (
                        <>
                          <button
                            onClick={() =>
                              handleStatusChange(booking.id, "confirmed")
                            }
                            className="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2 shadow-sm"
                          >
                            <CheckCircle className="w-4 h-4" /> Nhận việc
                          </button>
                          <button
                            onClick={() =>
                              handleStatusChange(booking.id, "cancelled")
                            }
                            className="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                          >
                            <XCircle className="w-4 h-4" /> Từ chối
                          </button>
                        </>
                      )}
                      {booking.status === "confirmed" && (
                        <button
                          onClick={() =>
                            handleStatusChange(booking.id, "completed")
                          }
                          className="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2 shadow-sm"
                        >
                          <CheckCircle className="w-4 h-4" /> Đã hoàn thành
                        </button>
                      )}
                      {(booking.status === "completed" ||
                        booking.status === "cancelled") && (
                        <button className="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                          Xem chi tiết
                        </button>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            ))}

            {bookings.length === 0 && (
              <div className="text-center py-20 bg-white rounded-2xl border border-gray-100">
                <CalendarCheck className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                <h3 className="text-lg font-medium text-gray-900">
                  Không có đơn đặt lịch nào
                </h3>
                <p className="text-gray-500 mt-2">
                  Khi có khách hàng đặt dịch vụ, thông tin sẽ hiển thị ở đây.
                </p>
              </div>
            )}
          </div>
        </main>
      </div>
    </div>
  );
}
