# CHECKLIST TỔNG HỢP - Website Dịch Vụ Địa Phương

> Thực hiện theo thứ tự từ trên xuống dưới. Mỗi phase phụ thuộc vào phase trước.
> Đánh dấu ✅ khi hoàn thành, ⬜ là chưa làm, 🔄 đang làm.

---

## PHASE 1: Eloquent Models & Relationships

> Nền tảng cho toàn bộ backend. Phải hoàn thành trước khi làm bất kỳ feature nào.

### 1.1 Models cơ bản (đã có)
- [x] `User` model
- [x] `ServiceCategory` model
- [x] `ServiceSubcategory` model

### 1.2 Models cần tạo mới
- [ ] `UserProfile` — thông tin mở rộng khách hàng (ngay_sinh, gioi_tinh, anh_dai_dien, dia_chi)
- [ ] `ProviderProfile` — hồ sơ nhà cung cấp (ten_hien_thi, loai, kinh_nghiem, xac_minh, diem)
- [ ] `Service` — dịch vụ (ten, slug, mo_ta, gia, dia_chi, trang_thai)
- [ ] `ServiceImage` — ảnh dịch vụ (duong_dan_anh, la_anh_dai_dien, thu_tu)
- [ ] `ServiceAttribute` — thuộc tính dịch vụ (ten_thuoc_tinh, gia_tri JSON)
- [ ] `ServiceArea` — khu vực phục vụ (phuong_xa, quan_huyen, ban_kinh_km)
- [ ] `ServiceSchedule` — lịch làm việc (thu_trong_tuan, gio_bat_dau, gio_ket_thuc)
- [ ] `ServiceTag` — tag dịch vụ (ten_tag, slug)
- [ ] `ServiceTagMap` — pivot bảng service ↔ tag
- [ ] `Booking` — đặt dịch vụ (ma_booking, ngay_dat, gio, trang_thai, tong_tien)
- [ ] `BookingStatusLog` — lịch sử trạng thái booking
- [ ] `Payment` — thanh toán (phuong_thuc, ma_giao_dich, so_tien, trang_thai)
- [ ] `Refund` — hoàn tiền (so_tien_hoan, ly_do, trang_thai)
- [ ] `Review` — đánh giá (so_sao, noi_dung, an_danh)
- [ ] `ReviewReply` — phản hồi đánh giá từ provider
- [ ] `Favorite` — yêu thích dịch vụ
- [ ] `Report` — báo cáo vi phạm
- [ ] `Notification` — thông báo
- [ ] `SearchHistory` — lịch sử tìm kiếm
- [ ] `ServiceView` — lượt xem dịch vụ
- [ ] `RecommendationLog` — log gợi ý
- [ ] `ItineraryRequest` — yêu cầu AI lập lịch trình
- [ ] `ItineraryItem` — chi tiết lịch trình

### 1.3 Định nghĩa Relationships
- [ ] `User` → hasOne `UserProfile`, hasOne `ProviderProfile`
- [ ] `User` → hasMany `Booking` (as customer), hasMany `Favorite`, hasMany `Notification`
- [ ] `ProviderProfile` → hasMany `Service`
- [ ] `Service` → belongsTo `ProviderProfile`, belongsTo `ServiceSubcategory`
- [ ] `Service` → hasMany `ServiceImage`, `ServiceAttribute`, `ServiceArea`, `ServiceSchedule`
- [ ] `Service` → belongsToMany `ServiceTag` through `ServiceTagMap`
- [ ] `Service` → hasMany `Booking`, `Review`, `ServiceView`
- [ ] `Booking` → belongsTo `Service`, `User` (customer), `ProviderProfile`
- [ ] `Booking` → hasMany `BookingStatusLog`, hasOne `Payment`, hasOne `Review`
- [ ] `Review` → belongsTo `Booking`, `Service`, `User` (customer), `ProviderProfile`
- [ ] `Review` → hasOne `ReviewReply`
- [ ] `Payment` → hasOne `Refund`
- [ ] `ServiceCategory` → hasMany `ServiceSubcategory`
- [ ] `ServiceSubcategory` → hasMany `Service`

### 1.4 Model Casts & Accessors
- [ ] Định nghĩa `$fillable`, `$casts`, `$hidden` cho mỗi model
- [ ] JSON cast cho: `ServiceAttribute.gia_tri`, `ProviderProfile.giay_phep`, `ItineraryRequest.so_thich`
- [ ] Date cast cho các trường ngày tháng
- [ ] Enum cast cho các trường trạng thái (trang_thai, vai_tro, v.v.)

---

## PHASE 2: Factories & Seeders

> Tạo dữ liệu mẫu để phát triển UI và test.

### 2.1 Factories
- [ ] `ServiceCategoryFactory` — tạo danh mục dịch vụ mẫu
- [ ] `ServiceSubcategoryFactory` — tạo danh mục con
- [ ] `ServiceTagFactory` — tạo tag mẫu
- [ ] `ProviderProfileFactory` — tạo hồ sơ provider mẫu
- [ ] `UserProfileFactory` — tạo hồ sơ khách hàng mẫu
- [ ] `ServiceFactory` — tạo dịch vụ mẫu (với gia, dia_chi, trang_thai)
- [ ] `ServiceImageFactory` — tạo ảnh mẫu
- [ ] `ServiceAreaFactory` — tạo khu vực phục vụ mẫu
- [ ] `ServiceScheduleFactory` — tạo lịch làm việc mẫu
- [ ] `BookingFactory` — tạo booking mẫu
- [ ] `ReviewFactory` — tạo đánh giá mẫu
- [ ] `PaymentFactory` — tạo thanh toán mẫu
- [ ] `NotificationFactory` — tạo thông báo mẫu

### 2.2 Seeders
- [ ] `ServiceCategorySeeder` — 8-10 danh mục thực tế (Du lịch, Sửa chữa, Vận chuyển, Ẩm thực, v.v.)
- [ ] `ServiceSubcategorySeeder` — 3-5 danh mục con mỗi danh mục
- [ ] `ServiceTagSeeder` — 20-30 tag phổ biến
- [ ] `UserSeeder` — 5 customers + 5 providers + 1 admin mẫu
- [ ] `ServiceSeeder` — 20-30 dịch vụ mẫu với ảnh, khu vực, lịch
- [ ] `BookingSeeder` — 15-20 booking mẫu ở nhiều trạng thái
- [ ] `ReviewSeeder` — 10-15 đánh giá mẫu
- [ ] `PaymentSeeder` — thanh toán cho booking đã hoàn thành
- [ ] Cập nhật `DatabaseSeeder` gọi tất cả seeder theo đúng thứ tự
- [ ] Chạy `php artisan migrate:fresh --seed` thành công

---

## PHASE 3: Service Layer & Repository cho Services (Dịch vụ)

> Xây dựng backend logic cho tính năng cốt lõi: quản lý dịch vụ.

### 3.1 DTOs
- [ ] `ServiceIndexData` — filter/sort cho danh sách dịch vụ (search, category, price range, location, rating)
- [ ] `ServiceCreateData` — dữ liệu tạo dịch vụ mới
- [ ] `ServiceUpdateData` — dữ liệu cập nhật dịch vụ
- [ ] `ServiceShowData` — dữ liệu hiển thị chi tiết dịch vụ

### 3.2 Repository
- [ ] `ServiceRepositoryInterface` — contract cho service repo
- [ ] `EloquentServiceRepository` — implementation với Eloquent
  - [ ] `getAll(filters)` — lấy danh sách có phân trang + filter
  - [ ] `getById(id)` — lấy chi tiết với eager load (images, areas, schedules, provider, reviews)
  - [ ] `getByProvider(providerId)` — lấy dịch vụ của provider
  - [ ] `create(data)` — tạo mới
  - [ ] `update(id, data)` — cập nhật
  - [ ] `delete(id)` — xóa (soft delete)
- [ ] Đăng ký binding trong `AppServiceProvider`

### 3.3 Service Class
- [ ] `ServiceService` (app/Services/Service/)
  - [ ] `listServices(ServiceIndexData)` — lấy danh sách + filter + paginate
  - [ ] `showService(id)` — lấy chi tiết đầy đủ
  - [ ] `createService(ServiceCreateData)` — tạo mới + upload ảnh + tạo areas + schedules
  - [ ] `updateService(id, ServiceUpdateData)` — cập nhật toàn bộ
  - [ ] `deleteService(id)` — xóa dịch vụ + cleanup ảnh
  - [ ] `getProviderServices(providerId)` — dịch vụ của provider

### 3.4 Form Requests (Validation)
- [ ] `ServiceIndexRequest` — validate query params (search, category_id, price_min, price_max, v.v.)
- [ ] `ServiceCreateRequest` — validate tạo dịch vụ (ten, mo_ta, gia, subcategory_id, images, v.v.)
- [ ] `ServiceUpdateRequest` — validate cập nhật dịch vụ

### 3.5 Controllers
- [ ] `ServiceController` (public) — index, show cho khách xem dịch vụ
- [ ] `Provider\ServiceController` — CRUD cho provider quản lý dịch vụ của mình

### 3.6 Vue Pages (cập nhật với data thực)
- [ ] `services/Index.vue` — nhận data từ controller, hiển thị danh sách + filter
- [ ] `services/Show.vue` — nhận data từ controller, hiển thị chi tiết dịch vụ
- [ ] `provider/services/Create.vue` — form tạo dịch vụ mới
- [ ] `provider/services/Edit.vue` — form sửa dịch vụ
- [ ] `provider/Services.vue` — danh sách dịch vụ của provider

### 3.7 Tests
- [ ] Unit test `ServiceService`
- [ ] Feature test `ServiceController` (public index, show)
- [ ] Feature test `Provider\ServiceController` (CRUD)
- [ ] Test authorization (provider chỉ sửa/xóa dịch vụ của mình)

---

## PHASE 4: File Upload System

> Upload ảnh dịch vụ, avatar người dùng.

### 4.1 Backend
- [ ] Tạo `ImageService` — xử lý upload, resize, validate
  - [ ] Validate file type (jpg, png, webp), max size (2MB)
  - [ ] Resize/optimize ảnh (thumbnail + full size)
  - [ ] Lưu vào `storage/app/public/services/` và `storage/app/public/avatars/`
  - [ ] Trả về đường dẫn lưu DB
- [ ] Tạo `ImageController` hoặc tích hợp vào ServiceController
- [ ] Chạy `php artisan storage:link` để tạo symlink public/storage

### 4.2 Frontend
- [ ] Component `ImageUpload.vue` — drag & drop, preview, multiple files
- [ ] Component `AvatarUpload.vue` — crop circle, single file
- [ ] Tích hợp vào form tạo/sửa dịch vụ
- [ ] Tích hợp vào trang profile

### 4.3 Tests
- [ ] Test upload ảnh hợp lệ
- [ ] Test reject file không hợp lệ (size, type)
- [ ] Test xóa ảnh khi xóa dịch vụ

---

## PHASE 5: Provider Profile & Dashboard

> Provider quản lý hồ sơ và xem tổng quan.

### 5.1 Backend
- [ ] `ProviderProfileData` DTO
- [ ] `ProviderProfileRepositoryInterface` + `EloquentProviderProfileRepository`
- [ ] `ProviderProfileService` — CRUD hồ sơ provider
- [ ] `ProviderProfileRequest` — validation (ten_hien_thi, loai, gioi_thieu, v.v.)
- [ ] `ProviderProfileController` — edit, update
- [ ] `ProviderDashboardController` — thống kê (tổng dịch vụ, booking, doanh thu, đánh giá)

### 5.2 Frontend
- [ ] `provider/Profile.vue` — form sửa hồ sơ provider (với data thực)
- [ ] `provider/Dashboard.vue` — dashboard với data thực (stats cards, recent bookings, chart)

### 5.3 Tests
- [ ] Test CRUD provider profile
- [ ] Test dashboard statistics

---

## PHASE 6: Customer Profile & Dashboard

> Khách hàng quản lý hồ sơ và xem tổng quan.

### 6.1 Backend
- [ ] `UserProfileData` DTO
- [ ] `UserProfileRepositoryInterface` + `EloquentUserProfileRepository`
- [ ] `UserProfileService` — CRUD hồ sơ khách hàng
- [ ] `UserProfileRequest` — validation
- [ ] `CustomerProfileController` — edit, update
- [ ] `CustomerDashboardController` — thống kê (booking gần đây, dịch vụ yêu thích)

### 6.2 Frontend
- [ ] `customer/Profile.vue` — form sửa hồ sơ (với data thực)
- [ ] `customer/Dashboard.vue` — dashboard với data thực

### 6.3 Tests
- [ ] Test CRUD customer profile
- [ ] Test dashboard data

---

## PHASE 7: Search & Filter System

> Tìm kiếm dịch vụ theo nhiều tiêu chí.

### 7.1 Backend
- [ ] `SearchData` DTO — keyword, category, subcategory, price range, location, rating, sort
- [ ] `SearchService` — full-text search + filter logic
  - [ ] Tìm theo tên dịch vụ (ILIKE/full-text PostgreSQL)
  - [ ] Filter theo danh mục / danh mục con
  - [ ] Filter theo khoảng giá
  - [ ] Filter theo quận/huyện
  - [ ] Filter theo đánh giá trung bình
  - [ ] Sort: mới nhất, giá tăng/giảm, đánh giá cao, gần nhất
  - [ ] Phân trang kết quả
- [ ] `SearchController` — xử lý tìm kiếm
- [ ] Lưu `SearchHistory` cho user đăng nhập (optional)

### 7.2 Frontend
- [ ] `search/Index.vue` — trang tìm kiếm với sidebar filter + results grid
- [ ] Component `SearchBar.vue` — thanh tìm kiếm tái sử dụng (header, hero)
- [ ] Component `ServiceCard.vue` — card hiển thị dịch vụ tái sử dụng
- [ ] Component `FilterSidebar.vue` — sidebar filter (category, price slider, rating stars)
- [ ] Component `Pagination.vue` — phân trang

### 7.3 Tests
- [ ] Test tìm kiếm theo keyword
- [ ] Test filter theo category, price, location
- [ ] Test sort
- [ ] Test phân trang

---

## PHASE 8: Booking System

> Đặt dịch vụ và quản lý booking.

### 8.1 Backend
- [ ] `BookingCreateData` DTO
- [ ] `BookingUpdateStatusData` DTO
- [ ] `BookingRepositoryInterface` + `EloquentBookingRepository`
- [ ] `BookingService`
  - [ ] `createBooking()` — tạo booking + generate ma_booking + tính tạm tính
  - [ ] `confirmBooking()` — provider xác nhận
  - [ ] `rejectBooking()` — provider từ chối + lý do
  - [ ] `cancelBooking()` — customer/provider hủy
  - [ ] `completeBooking()` — đánh dấu hoàn thành
  - [ ] `getCustomerBookings()` — lấy booking của customer
  - [ ] `getProviderBookings()` — lấy booking của provider
  - [ ] Mỗi thay đổi trạng thái → tạo `BookingStatusLog`
- [ ] `BookingCreateRequest` — validate (service_id, ngay_dat, gio, ghi_chu)
- [ ] `BookingStatusRequest` — validate thay đổi trạng thái
- [ ] `Customer\BookingController` — index, create, store, show, cancel
- [ ] `Provider\BookingController` — index, show, confirm, reject, complete

### 8.2 Frontend
- [ ] `customer/bookings/Create.vue` — form đặt dịch vụ (chọn ngày/giờ, ghi chú)
- [ ] `customer/bookings/Index.vue` — danh sách booking + filter trạng thái
- [ ] `customer/bookings/Show.vue` — chi tiết booking + nút hủy
- [ ] `provider/Bookings.vue` — danh sách booking nhận được
- [ ] `provider/bookings/Show.vue` — chi tiết + nút xác nhận/từ chối/hoàn thành
- [ ] Component `BookingStatusBadge.vue` — badge trạng thái (màu sắc theo status)
- [ ] Component `BookingTimeline.vue` — timeline lịch sử trạng thái

### 8.3 Tests
- [ ] Test tạo booking thành công
- [ ] Test booking status workflow (pending → confirmed → completed)
- [ ] Test hủy booking
- [ ] Test authorization (customer chỉ xem booking của mình, provider chỉ xem booking mình nhận)
- [ ] Test không thể đặt lịch ngày/giờ đã kín

---

## PHASE 9: Review & Rating System

> Đánh giá dịch vụ sau khi hoàn thành booking.

### 9.1 Backend
- [ ] `ReviewCreateData` DTO
- [ ] `ReviewReplyData` DTO
- [ ] `ReviewRepositoryInterface` + `EloquentReviewRepository`
- [ ] `ReviewService`
  - [ ] `createReview()` — tạo đánh giá (chỉ khi booking đã completed)
  - [ ] `replyToReview()` — provider trả lời
  - [ ] `getServiceReviews(serviceId)` — lấy đánh giá theo dịch vụ + paginate
  - [ ] `getProviderReviews(providerId)` — lấy đánh giá theo provider
  - [ ] `updateProviderRating()` — cập nhật diem_trung_binh và tong_danh_gia
- [ ] `ReviewCreateRequest` — validate (booking_id, so_sao 1-5, noi_dung, an_danh)
- [ ] `ReviewReplyRequest` — validate (review_id, noi_dung)
- [ ] `Customer\ReviewController` — create, store
- [ ] `Provider\ReviewController` — index, reply

### 9.2 Frontend
- [ ] `customer/reviews/Create.vue` — form đánh giá (stars, text, anonymous toggle)
- [ ] `provider/reviews/Index.vue` — danh sách đánh giá + form trả lời
- [ ] Component `StarRating.vue` — component chọn/hiển thị sao
- [ ] Component `ReviewCard.vue` — card đánh giá tái sử dụng
- [ ] Tích hợp reviews vào `services/Show.vue`

### 9.3 Tests
- [ ] Test tạo review cho booking completed
- [ ] Test không tạo review cho booking chưa completed
- [ ] Test mỗi booking chỉ 1 review
- [ ] Test provider reply
- [ ] Test cập nhật điểm trung bình provider

---

## PHASE 10: Favorites System

> Yêu thích / bookmark dịch vụ.

### 10.1 Backend
- [ ] `FavoriteService`
  - [ ] `toggleFavorite(userId, serviceId)` — thêm/xóa favorite
  - [ ] `getUserFavorites(userId)` — lấy danh sách yêu thích
  - [ ] `isFavorited(userId, serviceId)` — kiểm tra đã yêu thích chưa
- [ ] `Customer\FavoriteController` — index, toggle

### 10.2 Frontend
- [ ] `customer/favorites/Index.vue` — danh sách dịch vụ yêu thích
- [ ] Component `FavoriteButton.vue` — nút tim toggle (dùng ở ServiceCard, ServiceShow)
- [ ] Tích hợp FavoriteButton vào `services/Show.vue` và `ServiceCard.vue`

### 10.3 Tests
- [ ] Test toggle favorite
- [ ] Test danh sách favorites
- [ ] Test unauthorized access

---

## PHASE 11: Notification System

> Thông báo cho user về booking, review, v.v.

### 11.1 Backend
- [ ] `NotificationService`
  - [ ] `createNotification(userId, type, title, content)` — tạo thông báo
  - [ ] `getUserNotifications(userId)` — lấy danh sách (paginate)
  - [ ] `markAsRead(notificationId)` — đánh dấu đã đọc
  - [ ] `markAllAsRead(userId)` — đánh dấu tất cả đã đọc
  - [ ] `getUnreadCount(userId)` — đếm chưa đọc
- [ ] `NotificationController` — index, markRead, markAllRead
- [ ] Tích hợp tự động gửi notification khi:
  - [ ] Booking mới → provider nhận thông báo
  - [ ] Booking confirmed/rejected → customer nhận thông báo
  - [ ] Booking completed → customer nhận nhắc review
  - [ ] Review mới → provider nhận thông báo

### 11.2 Frontend
- [ ] `customer/notifications/Index.vue` — trang thông báo (với data thực)
- [ ] Component `NotificationBell.vue` — icon chuông + badge số chưa đọc (header)
- [ ] Component `NotificationItem.vue` — item thông báo
- [ ] Tích hợp NotificationBell vào AppHeader

### 11.3 Tests
- [ ] Test tạo notification
- [ ] Test mark as read
- [ ] Test auto-notification khi booking status thay đổi

---

## PHASE 12: Welcome Page & Categories Page

> Trang chủ và trang danh mục với data thực.

### 12.1 Backend
- [ ] `WelcomeController` — lấy featured categories, popular services, stats
- [ ] Cập nhật `CategoryController` — lấy categories + số dịch vụ mỗi category

### 12.2 Frontend
- [ ] `Welcome.vue` — hiển thị data thực (featured services, categories, hero search)
- [ ] `categories/Index.vue` — danh mục + subcategories + số dịch vụ

### 12.3 Tests
- [ ] Test welcome page data
- [ ] Test categories data

---

## PHASE 13: Authorization & Middleware

> Phân quyền chi tiết.

### 13.1 Policies
- [ ] `ServicePolicy` — chỉ provider sở hữu mới sửa/xóa dịch vụ
- [ ] `BookingPolicy` — customer chỉ xem/hủy booking mình, provider chỉ xem/xử lý booking mình nhận
- [ ] `ReviewPolicy` — customer chỉ review booking mình, provider chỉ reply review mình nhận
- [ ] `ProviderProfilePolicy` — chỉ chủ sở hữu sửa profile

### 13.2 Middleware
- [ ] `EnsureIsProvider` middleware — kiểm tra user có vai_tro = provider
- [ ] `EnsureIsCustomer` middleware — kiểm tra user có vai_tro = customer
- [ ] Áp dụng middleware cho route groups (provider/*, customer/*)

### 13.3 Tests
- [ ] Test provider không truy cập được route customer
- [ ] Test customer không truy cập được route provider
- [ ] Test user không sửa/xóa được resource của người khác

---

## PHASE 14: Payment Integration

> Tích hợp thanh toán.

### 14.1 Backend
- [ ] Chọn payment gateway (VNPay / MoMo / ZaloPay)
- [ ] `PaymentService`
  - [ ] `createPayment(bookingId, method)` — tạo giao dịch
  - [ ] `processPayment()` — xử lý thanh toán (redirect to gateway)
  - [ ] `handleCallback()` — xử lý callback từ gateway (IPN)
  - [ ] `refundPayment(paymentId, amount, reason)` — hoàn tiền
- [ ] `PaymentController` — create, callback, status
- [ ] Webhook endpoint cho payment gateway callback

### 14.2 Frontend
- [ ] Component `PaymentForm.vue` — chọn phương thức thanh toán
- [ ] Trang `payment/Success.vue` — thanh toán thành công
- [ ] Trang `payment/Failed.vue` — thanh toán thất bại
- [ ] Tích hợp vào flow booking

### 14.3 Tests
- [ ] Test tạo payment
- [ ] Test callback xử lý đúng
- [ ] Test refund

---

## PHASE 15: Email Notifications

> Gửi email cho các sự kiện quan trọng.

### 15.1 Backend
- [ ] Cấu hình mail driver (SMTP / Mailgun / SES) cho production
- [ ] `BookingConfirmedMail` — email khi booking được xác nhận
- [ ] `BookingCancelledMail` — email khi booking bị hủy
- [ ] `BookingCompletedMail` — email khi booking hoàn thành
- [ ] `NewBookingMail` — email cho provider khi có booking mới
- [ ] `ReviewReceivedMail` — email cho provider khi nhận review
- [ ] `WelcomeMail` — email chào mừng đăng ký
- [ ] Queue emails qua database queue (`ShouldQueue`)

### 15.2 Templates
- [ ] Tạo email Blade templates (responsive HTML email)
- [ ] Branding: logo, màu sắc, footer

### 15.3 Tests
- [ ] Test mỗi mailable render đúng
- [ ] Test emails được queue

---

## PHASE 16: Report System

> Báo cáo vi phạm.

### 16.1 Backend
- [ ] `ReportService` — tạo report (service/review/user)
- [ ] `ReportCreateRequest` — validate
- [ ] `ReportController` — store

### 16.2 Frontend
- [ ] Component `ReportButton.vue` — nút báo cáo + modal (lý do, mô tả)
- [ ] Tích hợp vào ServiceShow, ReviewCard

### 16.3 Tests
- [ ] Test tạo report

---

## PHASE 17: Admin Dashboard

> Quản trị viên quản lý hệ thống.

### 17.1 Backend
- [ ] `EnsureIsAdmin` middleware
- [ ] `Admin\DashboardController` — tổng quan (users, services, bookings, revenue)
- [ ] `Admin\UserController` — CRUD users, khóa/mở khóa tài khoản
- [ ] `Admin\ServiceController` — duyệt dịch vụ (cho_duyet → approved), ẩn dịch vụ vi phạm
- [ ] `Admin\BookingController` — xem tất cả bookings
- [ ] `Admin\CategoryController` — CRUD danh mục, danh mục con
- [ ] `Admin\ReportController` — xem/xử lý reports
- [ ] `Admin\ReviewController` — xem/ẩn review vi phạm

### 17.2 Frontend
- [ ] Layout `AdminLayout.vue` — sidebar admin navigation
- [ ] `admin/Dashboard.vue` — tổng quan thống kê + charts
- [ ] `admin/Users.vue` — bảng danh sách users + actions
- [ ] `admin/Services.vue` — bảng dịch vụ chờ duyệt + actions
- [ ] `admin/Bookings.vue` — bảng bookings
- [ ] `admin/Categories.vue` — quản lý danh mục
- [ ] `admin/Reports.vue` — xem/xử lý reports
- [ ] `admin/Reviews.vue` — quản lý reviews

### 17.3 Routes
- [ ] Route group `admin/*` với middleware `auth`, `verified`, `ensureIsAdmin`

### 17.4 Tests
- [ ] Test admin authorization
- [ ] Test duyệt dịch vụ
- [ ] Test khóa/mở tài khoản
- [ ] Test xử lý report

---

## PHASE 18: AI Trip Planner Integration

> Tích hợp AI lập lịch trình vào Laravel.

### 18.1 Backend
- [ ] Cấu hình Google GenAI API key (hoặc Claude API)
- [ ] `AIPlannerService`
  - [ ] `generateItinerary(request)` — gọi AI API + parse kết quả
  - [ ] `saveItinerary(userId, request, items)` — lưu vào DB
  - [ ] `getUserItineraries(userId)` — lịch sử lập lịch
- [ ] `AIPlannerController` — form, generate, history
- [ ] `AIPlannerRequest` — validate (ngay, ngan_sach, so_nguoi, so_thich)

### 18.2 Frontend
- [ ] `ai-planner/Index.vue` — form nhập + hiển thị kết quả (tích hợp data thực)
- [ ] Component `ItineraryCard.vue` — card ngày + hoạt động
- [ ] Liên kết dịch vụ trong lịch trình → service detail page

### 18.3 Tests
- [ ] Test API integration (mock external API)
- [ ] Test save itinerary
- [ ] Test validation

---

## PHASE 19: Analytics & Tracking

> Theo dõi và phân tích.

### 19.1 Backend
- [ ] `AnalyticsService`
  - [ ] `trackServiceView(userId, serviceId, duration)` — lưu lượt xem
  - [ ] `trackSearch(userId, keyword, filters)` — lưu lịch sử tìm kiếm
  - [ ] `getProviderAnalytics(providerId)` — thống kê cho provider
  - [ ] `getPopularServices()` — dịch vụ phổ biến
  - [ ] `getRecommendations(userId)` — gợi ý dựa trên history
- [ ] Middleware hoặc event listener để tự động track views

### 19.2 Frontend
- [ ] Cập nhật provider dashboard với charts (booking trend, revenue, views)
- [ ] Hiển thị "Dịch vụ phổ biến" trên Welcome page
- [ ] Hiển thị "Gợi ý cho bạn" trên customer dashboard

### 19.3 Tests
- [ ] Test track view
- [ ] Test popular services query
- [ ] Test recommendation logic

---

## PHASE 20: SEO & Performance

> Tối ưu SEO và hiệu suất.

### 20.1 SEO
- [ ] Meta tags động cho mỗi trang (title, description, og:image)
- [ ] Tạo `sitemap.xml` tự động (danh mục, dịch vụ, trang tĩnh)
- [ ] Tạo `robots.txt`
- [ ] Structured data (JSON-LD) cho: Service, LocalBusiness, Review, AggregateRating
- [ ] Canonical URLs
- [ ] Breadcrumb schema

### 20.2 Performance
- [ ] Lazy loading ảnh (loading="lazy")
- [ ] Image optimization (WebP format, responsive sizes)
- [ ] Database query optimization (N+1 queries, eager loading)
- [ ] Caching strategy (cache popular services, categories)
- [ ] Pagination cho tất cả danh sách

### 20.3 Tests
- [ ] Test meta tags render đúng
- [ ] Test sitemap generation

---

## PHASE 21: Security Hardening

> Bảo mật nâng cao.

- [ ] Cấu hình HTTPS enforcement (production)
- [ ] Content Security Policy (CSP) headers
- [ ] CORS configuration (nếu cần API)
- [ ] Rate limiting cho tất cả endpoints quan trọng
- [ ] Input sanitization cho rich text (nếu có)
- [ ] Account lockout sau nhiều lần login fail
- [ ] Audit logging cho admin actions
- [ ] Secure file upload (virus scan, file type validation)
- [ ] SQL injection review (đảm bảo dùng Eloquent/query builder)
- [ ] XSS review (đảm bảo escape output)

---

## PHASE 22: Testing Toàn Diện

> Bổ sung test coverage.

- [ ] Unit tests cho tất cả Service classes
- [ ] Unit tests cho tất cả Repository classes
- [ ] Feature tests cho tất cả Controller endpoints
- [ ] Feature tests cho authorization (Policies)
- [ ] Browser tests (Laravel Dusk) cho critical flows:
  - [ ] Đăng ký → đăng nhập → tìm dịch vụ → đặt booking
  - [ ] Provider tạo dịch vụ → nhận booking → hoàn thành
  - [ ] Customer review → provider reply
- [ ] CI/CD chạy toàn bộ tests trước merge

---

## PHASE 23: Deployment & Production

> Triển khai production.

- [ ] Chọn hosting (VPS / DigitalOcean / AWS)
- [ ] Setup PostgreSQL production
- [ ] Cấu hình environment variables production (.env)
- [ ] Setup domain + SSL certificate
- [ ] Cấu hình Nginx/Apache
- [ ] Setup queue worker (supervisor)
- [ ] Setup cron cho scheduled tasks (`php artisan schedule:run`)
- [ ] Cấu hình mail driver production
- [ ] Cấu hình backup tự động (database + storage)
- [ ] Setup monitoring (uptime, error tracking - Sentry)
- [ ] Setup logging (centralized logs)
- [ ] CI/CD pipeline deploy tự động
- [ ] Load testing
- [ ] Final security audit

---

## PHASE 24: Post-Launch

> Sau khi ra mắt.

- [ ] Monitor lỗi và fix bugs
- [ ] Thu thập feedback người dùng
- [ ] Tối ưu dựa trên analytics
- [ ] Mobile app (React Native / Flutter) — nếu cần
- [ ] Multi-language support (i18n) — nếu cần phục vụ du khách nước ngoài
- [ ] Chat/Messaging giữa customer và provider
- [ ] Provider verification workflow (upload giấy phép, admin duyệt)
- [ ] Coupon/Discount system
- [ ] Provider subscription plans

---

## TÓM TẮT TIẾN ĐỘ

| Phase | Mô tả | Trạng thái |
|-------|--------|-----------|
| 1 | Eloquent Models & Relationships | ⬜ Chưa làm |
| 2 | Factories & Seeders | ⬜ Chưa làm |
| 3 | Service Layer cho Dịch vụ | ⬜ Chưa làm |
| 4 | File Upload System | ⬜ Chưa làm |
| 5 | Provider Profile & Dashboard | ⬜ Chưa làm |
| 6 | Customer Profile & Dashboard | ⬜ Chưa làm |
| 7 | Search & Filter | ⬜ Chưa làm |
| 8 | Booking System | ⬜ Chưa làm |
| 9 | Review & Rating | ⬜ Chưa làm |
| 10 | Favorites | ⬜ Chưa làm |
| 11 | Notifications | ⬜ Chưa làm |
| 12 | Welcome & Categories (data thực) | ⬜ Chưa làm |
| 13 | Authorization & Middleware | ⬜ Chưa làm |
| 14 | Payment Integration | ⬜ Chưa làm |
| 15 | Email Notifications | ⬜ Chưa làm |
| 16 | Report System | ⬜ Chưa làm |
| 17 | Admin Dashboard | ⬜ Chưa làm |
| 18 | AI Trip Planner | ⬜ Chưa làm |
| 19 | Analytics & Tracking | ⬜ Chưa làm |
| 20 | SEO & Performance | ⬜ Chưa làm |
| 21 | Security Hardening | ⬜ Chưa làm |
| 22 | Testing Toàn Diện | ⬜ Chưa làm |
| 23 | Deployment & Production | ⬜ Chưa làm |
| 24 | Post-Launch | ⬜ Chưa làm |
