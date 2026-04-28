import { Link } from "react-router-dom";
import {
  BarChart3,
  Users,
  CalendarCheck,
  DollarSign,
  TrendingUp,
  Briefcase,
  Settings,
  LogOut,
  Star,
} from "lucide-react";

export default function ProviderDashboard() {
  const stats = [
    {
      title: "Tổng doanh thu",
      value: "24.500.000đ",
      icon: <DollarSign className="w-6 h-6 text-green-600" />,
      trend: "+12%",
      trendUp: true,
    },
    {
      title: "Job hoàn thành",
      value: "156",
      icon: <CalendarCheck className="w-6 h-6 text-indigo-600" />,
      trend: "+5%",
      trendUp: true,
    },
    {
      title: "Khách hàng mới",
      value: "42",
      icon: <Users className="w-6 h-6 text-blue-600" />,
      trend: "-2%",
      trendUp: false,
    },
    {
      title: "Đánh giá TB",
      value: "4.8",
      icon: <Star className="w-6 h-6 text-yellow-500" />,
      trend: "+0.1",
      trendUp: true,
    },
  ];

  const recentBookings = [
    {
      id: "BK-1030",
      customer: "Trần Thị B",
      service: "Vệ sinh máy lạnh",
      date: "16/10/2026",
      status: "pending",
      amount: 200000,
    },
    {
      id: "BK-1029",
      customer: "Nguyễn Văn A",
      service: "Sửa điện nước",
      date: "15/10/2026",
      status: "confirmed",
      amount: 150000,
    },
    {
      id: "BK-1028",
      customer: "Lê Văn C",
      service: "Sửa tủ lạnh",
      date: "14/10/2026",
      status: "completed",
      amount: 350000,
    },
  ];

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
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700 transition-colors"
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
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
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
        <main className="flex-1 space-y-8">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900">
              Tổng quan hoạt động
            </h1>
            <select className="bg-white border border-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option>Tháng này</option>
              <option>Tháng trước</option>
              <option>Năm nay</option>
            </select>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {stats.map((stat, idx) => (
              <div
                key={idx}
                className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
              >
                <div className="flex justify-between items-start mb-4">
                  <div
                    className={`p-3 rounded-xl ${stat.icon.props.className.includes("green") ? "bg-green-50" : stat.icon.props.className.includes("indigo") ? "bg-indigo-50" : stat.icon.props.className.includes("blue") ? "bg-blue-50" : "bg-yellow-50"}`}
                  >
                    {stat.icon}
                  </div>
                  <span
                    className={`flex items-center text-sm font-medium ${stat.trendUp ? "text-green-600" : "text-red-600"}`}
                  >
                    {stat.trendUp ? (
                      <TrendingUp className="w-4 h-4 mr-1" />
                    ) : (
                      <TrendingUp className="w-4 h-4 mr-1 rotate-180" />
                    )}
                    {stat.trend}
                  </span>
                </div>
                <h3 className="text-gray-500 text-sm font-medium mb-1">
                  {stat.title}
                </h3>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
              </div>
            ))}
          </div>

          {/* Recent Bookings */}
          <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex justify-between items-center">
              <h2 className="text-lg font-bold text-gray-900">
                Booking gần đây
              </h2>
              <Link
                to="/provider/bookings"
                className="text-sm font-medium text-indigo-600 hover:text-indigo-800"
              >
                Xem tất cả
              </Link>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead>
                  <tr className="bg-gray-50 text-gray-500 text-sm uppercase tracking-wider">
                    <th className="p-4 font-medium">Mã ĐH</th>
                    <th className="p-4 font-medium">Khách hàng</th>
                    <th className="p-4 font-medium">Dịch vụ</th>
                    <th className="p-4 font-medium">Ngày</th>
                    <th className="p-4 font-medium">Trạng thái</th>
                    <th className="p-4 font-medium text-right">Số tiền</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {recentBookings.map((booking) => (
                    <tr
                      key={booking.id}
                      className="hover:bg-gray-50 transition-colors"
                    >
                      <td className="p-4 text-sm font-medium text-gray-900">
                        {booking.id}
                      </td>
                      <td className="p-4 text-sm text-gray-600">
                        {booking.customer}
                      </td>
                      <td className="p-4 text-sm text-gray-600">
                        {booking.service}
                      </td>
                      <td className="p-4 text-sm text-gray-600">
                        {booking.date}
                      </td>
                      <td className="p-4 text-sm">
                        {booking.status === "pending" && (
                          <span className="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium">
                            Chờ xác nhận
                          </span>
                        )}
                        {booking.status === "confirmed" && (
                          <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs font-medium">
                            Đã xác nhận
                          </span>
                        )}
                        {booking.status === "completed" && (
                          <span className="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium">
                            Hoàn thành
                          </span>
                        )}
                      </td>
                      <td className="p-4 text-sm font-bold text-gray-900 text-right">
                        {new Intl.NumberFormat("vi-VN", {
                          style: "currency",
                          currency: "VND",
                        }).format(booking.amount)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
