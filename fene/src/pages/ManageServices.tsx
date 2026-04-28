import { useState } from "react";
import { Link } from "react-router-dom";
import {
  Plus,
  Edit,
  Trash2,
  Search,
  MoreVertical,
  Briefcase,
  BarChart3,
  CalendarCheck,
  Settings,
  LogOut,
  Star,
} from "lucide-react";

export default function ManageServices() {
  const [services, setServices] = useState([
    {
      id: 1,
      title: "Vệ sinh máy lạnh treo tường",
      category: "Sửa chữa",
      price: 200000,
      status: "active",
      views: 1240,
      bookings: 45,
    },
    {
      id: 2,
      title: "Sửa chữa tủ lạnh inverter",
      category: "Sửa chữa",
      price: 350000,
      status: "active",
      views: 850,
      bookings: 22,
    },
    {
      id: 3,
      title: "Bảo dưỡng máy giặt lồng ngang",
      category: "Sửa chữa",
      price: 400000,
      status: "inactive",
      views: 520,
      bookings: 10,
    },
  ]);

  const handleDelete = (id: number) => {
    if (confirm("Bạn có chắc chắn muốn xóa dịch vụ này?")) {
      setServices(services.filter((s) => s.id !== id));
    }
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
                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700 transition-colors"
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
        <main className="flex-1 space-y-6">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 className="text-2xl font-bold text-gray-900">
              Quản lý dịch vụ
            </h1>
            <button className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
              <Plus className="w-4 h-4" /> Thêm dịch vụ mới
            </button>
          </div>

          <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between gap-4">
              <div className="relative w-full sm:w-64">
                <Search className="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  placeholder="Tìm kiếm dịch vụ..."
                  className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-sm"
                />
              </div>
              <div className="flex gap-2">
                <select className="bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                  <option>Tất cả trạng thái</option>
                  <option>Đang hoạt động</option>
                  <option>Tạm ẩn</option>
                </select>
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead>
                  <tr className="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th className="p-4 font-medium">Tên dịch vụ</th>
                    <th className="p-4 font-medium">Danh mục</th>
                    <th className="p-4 font-medium text-right">Giá cơ bản</th>
                    <th className="p-4 font-medium text-center">Trạng thái</th>
                    <th className="p-4 font-medium text-right">Lượt xem</th>
                    <th className="p-4 font-medium text-right">Đã đặt</th>
                    <th className="p-4 font-medium text-center">Thao tác</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {services.map((service) => (
                    <tr
                      key={service.id}
                      className="hover:bg-gray-50 transition-colors"
                    >
                      <td className="p-4 text-sm font-medium text-gray-900">
                        {service.title}
                      </td>
                      <td className="p-4 text-sm text-gray-600">
                        {service.category}
                      </td>
                      <td className="p-4 text-sm font-bold text-gray-900 text-right">
                        {new Intl.NumberFormat("vi-VN", {
                          style: "currency",
                          currency: "VND",
                        }).format(service.price)}
                      </td>
                      <td className="p-4 text-sm text-center">
                        {service.status === "active" ? (
                          <span className="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium">
                            Đang hoạt động
                          </span>
                        ) : (
                          <span className="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">
                            Tạm ẩn
                          </span>
                        )}
                      </td>
                      <td className="p-4 text-sm text-gray-600 text-right">
                        {service.views}
                      </td>
                      <td className="p-4 text-sm text-gray-600 text-right">
                        {service.bookings}
                      </td>
                      <td className="p-4 text-sm text-center">
                        <div className="flex items-center justify-center gap-2">
                          <button
                            className="p-1 text-gray-400 hover:text-indigo-600 transition-colors"
                            title="Sửa"
                          >
                            <Edit className="w-4 h-4" />
                          </button>
                          <button
                            onClick={() => handleDelete(service.id)}
                            className="p-1 text-gray-400 hover:text-red-600 transition-colors"
                            title="Xóa"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                          <button
                            className="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                            title="Thêm"
                          >
                            <MoreVertical className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                  {services.length === 0 && (
                    <tr>
                      <td colSpan={7} className="p-8 text-center text-gray-500">
                        Chưa có dịch vụ nào. Hãy thêm dịch vụ đầu tiên của bạn!
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>

            <div className="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
              <span>Hiển thị {services.length} dịch vụ</span>
              <div className="flex gap-1">
                <button className="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50">
                  Trước
                </button>
                <button className="px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded font-medium">
                  1
                </button>
                <button className="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50">
                  Sau
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
