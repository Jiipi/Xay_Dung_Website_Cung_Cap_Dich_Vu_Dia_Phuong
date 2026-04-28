import { Outlet, Link } from "react-router-dom";
import { Search, User, Menu, Map, Wrench, Briefcase } from "lucide-react";

export default function RootLayout() {
  return (
    <div className="min-h-screen bg-gray-50 flex flex-col font-sans">
      <header className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Link to="/" className="flex items-center gap-2">
                <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                  <span className="text-white font-bold text-xl">A</span>
                </div>
                <span className="text-xl font-bold text-gray-900">
                  AI Marketplace
                </span>
              </Link>
              <nav className="hidden md:flex ml-10 space-x-8">
                <Link
                  to="/services"
                  className="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                >
                  Khám phá
                </Link>
                <Link
                  to="/ai-planner"
                  className="text-indigo-600 hover:text-indigo-800 px-3 py-2 rounded-md text-sm font-medium flex items-center gap-1"
                >
                  <span className="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full font-bold">
                    AI
                  </span>{" "}
                  Lên lịch trình
                </Link>
              </nav>
            </div>
            <div className="flex items-center space-x-4">
              <div className="hidden md:flex items-center space-x-4">
                <Link
                  to="/customer/dashboard"
                  className="text-gray-500 hover:text-gray-900 text-sm font-medium"
                >
                  Khách hàng
                </Link>
                <Link
                  to="/provider/dashboard"
                  className="text-gray-500 hover:text-gray-900 text-sm font-medium"
                >
                  Nhà cung cấp
                </Link>
              </div>
              <button className="p-2 text-gray-400 hover:text-gray-500 md:hidden">
                <Menu className="w-6 h-6" />
              </button>
              <div className="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                <User className="w-5 h-5 text-gray-500" />
              </div>
            </div>
          </div>
        </div>
      </header>

      <main className="flex-1">
        <Outlet />
      </main>

      <footer className="bg-white border-t border-gray-200 mt-auto">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <span className="text-xl font-bold text-gray-900">
                AI Marketplace
              </span>
              <p className="mt-2 text-sm text-gray-500">
                Nền tảng kết nối dịch vụ và du lịch thông minh với AI.
              </p>
            </div>
            <div>
              <h3 className="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                Dịch vụ
              </h3>
              <ul className="mt-4 space-y-2">
                <li>
                  <Link
                    to="/services?category=du_lich"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Du lịch & Tour
                  </Link>
                </li>
                <li>
                  <Link
                    to="/services?category=sua_chua"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Sửa chữa
                  </Link>
                </li>
                <li>
                  <Link
                    to="/services?category=thue_xe"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Thuê xe
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                Hỗ trợ
              </h3>
              <ul className="mt-4 space-y-2">
                <li>
                  <a
                    href="#"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Trung tâm trợ giúp
                  </a>
                </li>
                <li>
                  <a
                    href="#"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Quy định an toàn
                  </a>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="text-sm font-semibold text-gray-900 tracking-wider uppercase">
                Đối tác
              </h3>
              <ul className="mt-4 space-y-2">
                <li>
                  <Link
                    to="/provider/dashboard"
                    className="text-sm text-gray-500 hover:text-gray-900"
                  >
                    Đăng ký nhà cung cấp
                  </Link>
                </li>
              </ul>
            </div>
          </div>
          <div className="mt-8 border-t border-gray-200 pt-8 flex justify-between items-center">
            <p className="text-sm text-gray-400">
              &copy; 2026 AI Marketplace. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}
