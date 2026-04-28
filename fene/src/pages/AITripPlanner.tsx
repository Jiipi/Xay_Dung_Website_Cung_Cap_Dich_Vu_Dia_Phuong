import React, { useState } from "react";
import {
  Sparkles,
  MapPin,
  Calendar,
  DollarSign,
  Users,
  Send,
  Loader2,
} from "lucide-react";

export default function AITripPlanner() {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<any>(null);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    // Simulate AI processing
    setTimeout(() => {
      setResult({
        title: "Lịch trình 3 ngày 2 đêm tại Đà Lạt",
        budget: "3.500.000đ/người",
        days: [
          {
            day: 1,
            activities: [
              {
                time: "08:00",
                name: "Đến sân bay Liên Khương, di chuyển về trung tâm",
                cost: "250.000đ",
              },
              {
                time: "10:00",
                name: "Nhận phòng tại Homestay Gió Mây",
                cost: "500.000đ/đêm",
              },
              {
                time: "12:00",
                name: "Ăn trưa Lẩu Gà Lá É Tao Ngộ",
                cost: "150.000đ",
              },
              {
                time: "14:00",
                name: "Tham quan Dinh 1 Bảo Đại",
                cost: "50.000đ",
              },
              {
                time: "19:00",
                name: "Ăn tối BBQ tại Tiệm Nướng Thương Thương",
                cost: "300.000đ",
              },
            ],
          },
          {
            day: 2,
            activities: [
              {
                time: "05:00",
                name: "Tour Săn Mây Đồi Chè Cầu Đất (Dịch vụ gợi ý)",
                cost: "450.000đ",
                recommendedServiceId: 1,
              },
              {
                time: "12:00",
                name: "Ăn trưa Bánh Ướt Lòng Gà Trang",
                cost: "60.000đ",
              },
              { time: "15:00", name: "Cà phê Túi Mơ To", cost: "80.000đ" },
              {
                time: "19:00",
                name: "Dạo chợ đêm Đà Lạt, ăn vặt",
                cost: "100.000đ",
              },
            ],
          },
        ],
      });
      setLoading(false);
    }, 2000);
  };

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center mb-10">
        <div className="inline-flex items-center justify-center p-3 bg-indigo-100 rounded-full mb-4">
          <Sparkles className="w-8 h-8 text-indigo-600" />
        </div>
        <h1 className="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
          AI Lên Lịch Trình & Gợi Ý Dịch Vụ
        </h1>
        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
          Chỉ cần cho chúng tôi biết bạn muốn đi đâu hoặc cần sửa chữa gì, AI sẽ
          tự động thiết kế lịch trình và tìm thợ phù hợp nhất cho bạn.
        </p>
      </div>

      <div className="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div className="p-8 md:p-10">
          <form onSubmit={handleSubmit} className="space-y-6">
            <div>
              <label className="block text-sm font-semibold text-gray-900 mb-2">
                Bạn muốn đi đâu / Cần dịch vụ gì?
              </label>
              <textarea
                rows={3}
                className="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all resize-none text-gray-900 placeholder-gray-400"
                placeholder="VD: Tôi muốn đi Đà Lạt 3 ngày 2 đêm cùng gia đình 4 người, thích thiên nhiên và ăn uống..."
                required
              ></textarea>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label className="block text-sm font-semibold text-gray-900 mb-2">
                  Địa điểm
                </label>
                <div className="relative">
                  <MapPin className="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    className="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                    placeholder="Đà Lạt"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-semibold text-gray-900 mb-2">
                  Thời gian
                </label>
                <div className="relative">
                  <Calendar className="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    className="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                    placeholder="3 ngày 2 đêm"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-semibold text-gray-900 mb-2">
                  Ngân sách dự kiến
                </label>
                <div className="relative">
                  <DollarSign className="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    className="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                    placeholder="5.000.000đ"
                  />
                </div>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
            >
              {loading ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" /> Đang phân tích
                  yêu cầu...
                </>
              ) : (
                <>
                  <Sparkles className="w-5 h-5" /> Tạo lịch trình bằng AI
                </>
              )}
            </button>
          </form>
        </div>

        {/* AI Result Section */}
        {result && (
          <div className="bg-indigo-50 border-t border-indigo-100 p-8 md:p-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div className="flex items-center justify-between mb-8">
              <div>
                <h2 className="text-2xl font-bold text-indigo-900">
                  {result.title}
                </h2>
                <p className="text-indigo-700 mt-1 font-medium">
                  Ngân sách ước tính: {result.budget}
                </p>
              </div>
              <button className="px-4 py-2 bg-white text-indigo-600 font-medium rounded-lg shadow-sm border border-indigo-100 hover:bg-indigo-50 transition-colors">
                Lưu lịch trình
              </button>
            </div>

            <div className="space-y-8">
              {result.days.map((day: any) => (
                <div
                  key={day.day}
                  className="relative pl-8 border-l-2 border-indigo-200"
                >
                  <div className="absolute -left-[11px] top-0 w-5 h-5 bg-indigo-600 rounded-full border-4 border-indigo-50"></div>
                  <h3 className="text-lg font-bold text-indigo-900 mb-4 -mt-1">
                    Ngày {day.day}
                  </h3>
                  <div className="space-y-4">
                    {day.activities.map((act: any, idx: number) => (
                      <div
                        key={idx}
                        className={`bg-white p-4 rounded-xl shadow-sm border ${act.recommendedServiceId ? "border-indigo-300 ring-1 ring-indigo-100" : "border-gray-100"} flex flex-col sm:flex-row sm:items-center justify-between gap-4`}
                      >
                        <div className="flex items-start gap-4">
                          <span className="text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md shrink-0">
                            {act.time}
                          </span>
                          <div>
                            <p className="font-medium text-gray-900">
                              {act.name}
                            </p>
                            <p className="text-sm text-gray-500 mt-1">
                              Chi phí dự kiến: {act.cost}
                            </p>
                          </div>
                        </div>
                        {act.recommendedServiceId && (
                          <button className="shrink-0 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            Đặt dịch vụ này
                          </button>
                        )}
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
