# Bộ câu hỏi vấn đáp bảo vệ dự án Laravel/Vue

## Cách dùng tài liệu

Tài liệu này dùng để ôn bảo vệ theo góc nhìn của giảng viên khó tính. Thứ tự học nên là:

1. Công cụ và công nghệ sử dụng.
2. Backend Laravel/PHP.
3. Frontend Vue/Inertia.
4. Luồng nghiệp vụ toàn bộ website.
5. Câu hỏi bẫy và hỏi sâu.

Khi trả lời, không chỉ nói “dự án dùng gì”, mà phải nói được “vì sao dùng”, “nó nằm ở file nào”, “nếu không dùng thì hậu quả gì”, và “hạn chế/rủi ro là gì”.

## 1. Nhóm ưu tiên cao: Công cụ và công nghệ sử dụng

### Câu 1. Dự án này dùng những công nghệ chính nào và mỗi công nghệ nằm ở lớp nào?

- Mức độ: Dễ nhưng rất hay hỏi.
- Vì sao giảng viên hỏi: Kiểm tra sinh viên có nắm tổng quan stack hay chỉ biết chạy project.
- Gợi ý trả lời:
  - Backend dùng PHP 8.2 và Laravel 12 để xử lý route, controller, middleware, validation, service, model, migration.
  - Auth dùng Laravel Fortify.
  - Frontend dùng Vue 3, TypeScript, Inertia.js, Vite và Tailwind CSS.
  - Database dùng PostgreSQL, quản lý cấu trúc bằng Laravel migrations.
  - Docker Compose chạy app, nginx, postgres, cloudbeaver và reverb.
  - Realtime dùng Laravel Reverb, Laravel Echo và Pusher protocol.
- Câu hỏi bẫy/hỏi tiếp:
  - Công nghệ nào chạy ở server, công nghệ nào chạy ở browser?
  - Inertia nằm ở backend hay frontend?
  - Docker có phải database không?

### Câu 2. Vì sao chọn Laravel thay vì viết PHP thuần?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Muốn xem sinh viên hiểu framework giải quyết vấn đề gì.
- Gợi ý trả lời:
  - PHP thuần phải tự tổ chức route, auth, validate, ORM, migration, middleware, test.
  - Laravel cung cấp sẵn cấu trúc MVC, routing, middleware, Eloquent ORM, migration, validation, service container, testing.
  - Với website marketplace có nhiều vai trò customer/provider/admin, Laravel giúp tổ chức code rõ hơn và dễ bảo trì.
- Câu hỏi bẫy/hỏi tiếp:
  - Dùng Laravel có làm project nặng hơn không?
  - Khi nào PHP thuần vẫn phù hợp?

### Câu 3. PHP trong dự án này đóng vai trò gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Kiểm tra hiểu server-side.
- Gợi ý trả lời:
  - PHP chạy phía server trong Laravel.
  - PHP nhận HTTP request, kiểm tra middleware, gọi controller/service, truy vấn database qua Eloquent và trả response Inertia/JSON/redirect.
  - PHP không trực tiếp render UI tương tác; UI chính nằm ở Vue.
- Câu hỏi bẫy/hỏi tiếp:
  - PHP chạy trên trình duyệt không?
  - Vue có thay thế PHP không?

### Câu 4. Composer dùng để làm gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Kiểm tra kiến thức mã nguồn mở PHP.
- Gợi ý trả lời:
  - Composer là trình quản lý package PHP.
  - `composer.json` khai báo Laravel, Fortify, Inertia Laravel, Reverb, PHPUnit, Pint.
  - Composer cũng quản lý autoload PSR-4, ví dụ namespace `App\` trỏ tới thư mục `app/`.
- Câu hỏi bẫy/hỏi tiếp:
  - `composer.lock` khác gì `composer.json`?
  - Vì sao không commit thư mục `vendor/`?

### Câu 5. PSR-4 autoload trong Laravel có ý nghĩa gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra hiểu tổ chức class PHP.
- Gợi ý trả lời:
  - PSR-4 ánh xạ namespace với thư mục.
  - Trong dự án, `App\` ánh xạ tới `app/`, nên class `App\Models\User` nằm ở `app/Models/User.php`.
  - Nhờ autoload, Laravel không cần `require` thủ công từng file.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu đổi namespace sai thì lỗi gì xảy ra?
  - Khi tạo class mới mà autoload lỗi thì xử lý thế nào?

### Câu 6. Laravel request lifecycle trong dự án diễn ra như thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Đây là câu kiểm tra hiểu kiến trúc tổng thể backend.
- Gợi ý trả lời:
  - Browser gửi request đến nginx/app.
  - Laravel nhận request, chạy middleware như `auth`, `verified`, role middleware.
  - Route trong `routes/web.php` ánh xạ tới controller.
  - Controller dùng Form Request để validate, gọi service xử lý nghiệp vụ.
  - Service gọi repository/model, Eloquent truy vấn PostgreSQL.
  - Kết quả trả về Inertia page, redirect hoặc JSON.
- Câu hỏi bẫy/hỏi tiếp:
  - Middleware chạy trước hay sau controller?
  - Validation nên đặt ở controller hay Form Request?

### Câu 7. Laravel Fortify là gì và dự án dùng Fortify để làm gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Auth là phần nhạy cảm của web.
- Gợi ý trả lời:
  - Fortify là backend authentication package của Laravel.
  - Nó cung cấp logic đăng ký, đăng nhập, reset password, email verification, two-factor authentication.
  - Dự án custom action đăng ký ở `app/Actions/Fortify/CreateNewUser.php` để tạo user theo vai trò customer/provider.
- Câu hỏi bẫy/hỏi tiếp:
  - Fortify có tự cung cấp giao diện không?
  - Fortify khác Breeze thế nào?

### Câu 8. Fortify khác Breeze, Sanctum và Passport thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Hay dùng để bắt lỗi học thuộc package.
- Gợi ý trả lời:
  - Fortify cung cấp backend auth action, không bắt buộc UI.
  - Breeze là starter kit có sẵn UI auth đơn giản.
  - Sanctum dùng cho API token hoặc SPA auth nhẹ.
  - Passport dùng OAuth2 đầy đủ, phù hợp hệ thống cấp token OAuth phức tạp.
  - Dự án này dùng Inertia/Vue nên Fortify phù hợp để custom UI riêng.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu làm mobile app thì Fortify session auth có đủ không?
  - Khi nào cần Sanctum?

### Câu 9. Inertia.js là gì trong dự án này?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Đây là cầu nối chính giữa Laravel và Vue.
- Gợi ý trả lời:
  - Inertia cho phép Laravel route/controller trả về Vue page mà không cần xây REST API đầy đủ cho từng màn hình.
  - Backend vẫn giữ routing, middleware, session auth, validation.
  - Frontend nhận props và render bằng Vue, tạo trải nghiệm gần SPA.
- Câu hỏi bẫy/hỏi tiếp:
  - Inertia có phải REST API không?
  - Inertia có thay thế Vue không?
  - Inertia có phải SSR mặc định không?

### Câu 10. Vì sao dùng Inertia thay vì tách Laravel API và Vue SPA riêng?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra khả năng giải thích lựa chọn kiến trúc.
- Gợi ý trả lời:
  - Dự án nhiều form, dashboard, phân quyền theo session, nên Inertia giúp tận dụng Laravel route/auth/validation.
  - Không cần viết API riêng cho mọi trang, giảm trùng lặp logic.
  - Vue vẫn đảm nhiệm UI tương tác, component, state cục bộ.
  - Nếu sau này làm mobile app hoặc public API, có thể bổ sung API riêng.
- Câu hỏi bẫy/hỏi tiếp:
  - Inertia có làm frontend phụ thuộc backend hơn không?
  - Khi nào API riêng tốt hơn?

### Câu 11. Vue 3 dùng để làm gì trong dự án?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Kiểm tra phân chia backend/frontend.
- Gợi ý trả lời:
  - Vue 3 xây dựng giao diện: page, layout, component, form, dashboard.
  - Vue nhận dữ liệu từ Inertia props và render UI.
  - Vue xử lý tương tác như dropdown, sidebar, form state, realtime notification/chat.
- Câu hỏi bẫy/hỏi tiếp:
  - Vue có trực tiếp truy vấn database không?
  - Vue có quyết định quyền truy cập thật sự không?

### Câu 12. Composition API trong Vue 3 có lợi gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra frontend hiện đại.
- Gợi ý trả lời:
  - Composition API dùng `ref`, `computed`, `watch`, composable để tổ chức logic linh hoạt hơn Options API.
  - Logic có thể tái sử dụng qua composables như realtime channel, appearance, smooth scroll.
  - Phù hợp TypeScript hơn vì type inference tốt.
- Câu hỏi bẫy/hỏi tiếp:
  - `ref` khác `reactive` thế nào?
  - Khi nào nên tách composable?

### Câu 13. TypeScript giúp gì cho frontend?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra hiểu type safety.
- Gợi ý trả lời:
  - TypeScript giúp khai báo kiểu cho props, user, service, booking, notification.
  - Giảm lỗi do sai tên field giữa backend props và frontend.
  - Hỗ trợ autocomplete và phát hiện lỗi trước khi build.
- Câu hỏi bẫy/hỏi tiếp:
  - TypeScript có kiểm tra dữ liệu runtime từ server không?
  - Dùng `any` nhiều có còn lợi ích TypeScript không?

### Câu 14. Vite dùng để làm gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Công cụ build frontend thường bị nhầm.
- Gợi ý trả lời:
  - Vite là dev server và bundler cho frontend.
  - Khi dev, Vite cung cấp HMR để thay đổi UI nhanh.
  - Khi production, `npm run build` tạo asset tối ưu cho browser.
- Câu hỏi bẫy/hỏi tiếp:
  - Vite có chạy PHP không?
  - Vite khác Webpack/Laravel Mix ở điểm nào?

### Câu 15. Tailwind CSS có ưu điểm và nhược điểm gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra lựa chọn UI framework.
- Gợi ý trả lời:
  - Ưu điểm: utility-first, responsive nhanh, dễ đồng bộ design, ít viết CSS custom.
  - Nhược điểm: class dài, nếu không tách component thì template khó đọc.
  - Trong dự án, Tailwind phù hợp vì nhiều page/dashboard cần responsive nhanh.
- Câu hỏi bẫy/hỏi tiếp:
  - Tailwind có thay thế tư duy CSS không?
  - Làm sao tránh lặp class quá nhiều?

### Câu 16. PostgreSQL trong dự án lưu dữ liệu gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Kiểm tra hiểu database thật của app.
- Gợi ý trả lời:
  - PostgreSQL lưu user, vai trò, dịch vụ, booking, đánh giá, thông báo, yêu thích, giao dịch, chat.
  - Các bảng chính gồm `nguoi_dung`, `vai_tro_nguoi_dung`, `dich_vu`, `don_dat_lich`, `danh_gia`, `thong_bao`, `conversations`, `messages`.
  - Schema được định nghĩa bằng Laravel migrations.
- Câu hỏi bẫy/hỏi tiếp:
  - Database nằm trong file code hay Docker volume?
  - CloudBeaver có phải database không?

### Câu 17. Docker Compose trong dự án có vai trò gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra khả năng chạy môi trường.
- Gợi ý trả lời:
  - Docker Compose định nghĩa nhiều service chạy cùng nhau: app, nginx, postgres, cloudbeaver, reverb.
  - Giúp môi trường thống nhất giữa các máy.
  - PostgreSQL lưu dữ liệu trong volume `dalat_pgdata`.
- Câu hỏi bẫy/hỏi tiếp:
  - Xóa volume có mất database không?
  - Trong CloudBeaver host DB là `postgres` hay `localhost`?

### Câu 18. CloudBeaver dùng để làm gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Hay hỏi khi demo database.
- Gợi ý trả lời:
  - CloudBeaver là giao diện web để xem/quản trị database.
  - Nó kết nối tới PostgreSQL service trong Docker bằng host `postgres`, port `5432`, database `dalat_services`.
  - Nó không phải nơi lưu DB; dữ liệu nằm ở PostgreSQL volume.
- Câu hỏi bẫy/hỏi tiếp:
  - Vì sao trong CloudBeaver không dùng `localhost`?
  - Nếu dùng DBeaver Desktop trên Windows thì host là gì?

### Câu 19. Laravel Reverb/Echo giải quyết vấn đề gì?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Realtime dễ bị nói chung chung.
- Gợi ý trả lời:
  - Reverb là websocket server của Laravel.
  - Echo là thư viện frontend subscribe channel và listen event.
  - Dự án dùng để realtime notification, booking update, chat giữa customer/provider.
  - Private channel giúp chỉ user được quyền mới nhận event.
- Câu hỏi bẫy/hỏi tiếp:
  - Websocket khác polling thế nào?
  - Nếu Reverb server chết thì UI nên xử lý ra sao?

### Câu 20. `ShouldBroadcastNow` khác queue broadcast bình thường thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra hiểu realtime backend.
- Gợi ý trả lời:
  - `ShouldBroadcastNow` phát event ngay trong request hiện tại, không đợi queue worker.
  - Ưu điểm là nhanh và dễ demo realtime.
  - Nhược điểm là request có thể chịu thêm chi phí broadcast; production lớn nên cân nhắc queue.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu dùng queue mà queue worker chết thì sao?
  - Có nên broadcast mọi thay đổi không?

### Câu 21. PHPUnit Feature Test kiểm tra điều gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra đảm bảo chất lượng.
- Gợi ý trả lời:
  - Feature Test kiểm tra một luồng chức năng qua HTTP/request của Laravel.
  - Ví dụ auth, settings, dashboard, category.
  - Nó phù hợp kiểm tra route, middleware, validation, database effect.
- Câu hỏi bẫy/hỏi tiếp:
  - Feature test khác unit test thế nào?
  - Test frontend UI có dùng PHPUnit được không?

### Câu 22. Laravel Pint, ESLint, Prettier và vue-tsc khác nhau thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Công cụ chất lượng code thường bị nhầm.
- Gợi ý trả lời:
  - Pint format/check style PHP.
  - ESLint kiểm tra lỗi/lint JavaScript/TypeScript/Vue.
  - Prettier format frontend resource.
  - `vue-tsc` kiểm tra type TypeScript trong Vue.
- Câu hỏi bẫy/hỏi tiếp:
  - Các tool này có thay thế test nghiệp vụ không?
  - Format đúng có đảm bảo code chạy đúng không?

### Câu 23. `composer ci:check` nên kiểm tra những gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra quy trình trước khi nộp/bảo vệ.
- Gợi ý trả lời:
  - Nên chạy lint PHP, format frontend, typecheck frontend và test Laravel.
  - Trong `composer.json`, `ci:check` gọi `npm run lint:check`, `npm run format:check`, `npm run types:check`, và test.
  - Mục tiêu là phát hiện lỗi trước khi merge/nộp.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu thiếu `node_modules` thì typecheck/build lỗi vì sao?
  - CI xanh có đảm bảo không có bug nghiệp vụ không?

### Câu 24. Migrations có vai trò gì so với ERD vẽ trong báo cáo?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Kiểm tra source có khớp báo cáo không.
- Gợi ý trả lời:
  - Migration là nguồn sự thật để tạo database thật.
  - ERD trong báo cáo phải đối chiếu với migration và model relationship.
  - Nếu ERD khác migration, khi chạy app thì database vẫn theo migration, không theo hình vẽ.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu sửa ERD mà không sửa migration thì app có thay đổi không?
  - FK nên thể hiện ở ERD hay chỉ trong code?

### Câu 25. Vì sao database dùng tên bảng tiếng Việt như `nguoi_dung`, `dich_vu`?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Có thể hỏi về quy ước đặt tên.
- Gợi ý trả lời:
  - Dự án hướng tới domain dịch vụ địa phương Việt Nam nên tên bảng tiếng Việt giúp gần nghiệp vụ.
  - Laravel vẫn hỗ trợ nếu model khai báo đúng table và relationship.
  - Nhược điểm là cần thống nhất naming để tránh lẫn Anh-Việt.
- Câu hỏi bẫy/hỏi tiếp:
  - Eloquent có tự đoán đúng tên bảng tiếng Việt không?
  - Khi làm dự án quốc tế nên đặt tên thế nào?

## 2. Nhóm Backend Laravel/PHP

### Câu 26. Backend của dự án được tổ chức theo những lớp nào?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Đây là xương sống kiến trúc backend.
- Gợi ý trả lời:
  - Route định nghĩa URL.
  - Controller nhận request và điều phối.
  - Form Request validate input.
  - Service xử lý nghiệp vụ.
  - Repository xử lý truy vấn phức tạp.
  - Model đại diện bảng và quan hệ.
  - Migration định nghĩa schema database.
- Câu hỏi bẫy/hỏi tiếp:
  - Có bắt buộc mọi logic đều phải qua repository không?
  - Controller có được query trực tiếp không?

### Câu 27. Vì sao controller nên mỏng?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra clean architecture cơ bản.
- Gợi ý trả lời:
  - Controller chỉ nên nhận request, authorize, validate và gọi service.
  - Nếu controller chứa nhiều nghiệp vụ thì khó test, khó tái sử dụng, khó bảo trì.
  - Service như `BookingService` có thể được gọi từ nhiều controller/use case.
- Câu hỏi bẫy/hỏi tiếp:
  - Controller mỏng quá có làm service phình to không?
  - Khi nào logic nhỏ có thể để trong controller?

### Câu 28. Service Layer trong dự án giải quyết vấn đề gì?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Muốn biết sinh viên có hiểu kiến trúc đang trình bày.
- Gợi ý trả lời:
  - Service chứa nghiệp vụ như booking lifecycle, quản lý dịch vụ, notification, dashboard.
  - Nó phối hợp nhiều model/repository và đảm bảo rule nghiệp vụ tập trung.
  - Ví dụ booking không chỉ insert DB mà còn kiểm tra trạng thái, tạo thông báo, broadcast update.
- Câu hỏi bẫy/hỏi tiếp:
  - Service khác model method thế nào?
  - Nếu service gọi quá nhiều model thì xử lý ra sao?

### Câu 29. Repository Pattern trong dự án có lợi gì?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Repository hay bị dùng máy móc.
- Gợi ý trả lời:
  - Repository tách query phức tạp khỏi service/controller.
  - Contract định nghĩa interface, Eloquent implementation xử lý query thật.
  - Hữu ích cho search/filter/dashboard/report hoặc khi muốn giảm phụ thuộc trực tiếp vào Eloquent trong service.
- Câu hỏi bẫy/hỏi tiếp:
  - Dùng repository cho mọi CRUD đơn giản có thể là overengineering không?
  - Interface có ích gì nếu chỉ có một implementation?

### Câu 30. DTO khác gì Form Request?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: DTO dễ bị hiểu nhầm là validation.
- Gợi ý trả lời:
  - Form Request kiểm tra dữ liệu HTTP đầu vào.
  - DTO đóng gói dữ liệu đã hợp lệ để truyền giữa các lớp nghiệp vụ.
  - DTO giúp tránh truyền mảng tự do và làm rõ cấu trúc dữ liệu.
- Câu hỏi bẫy/hỏi tiếp:
  - DTO có tự validate không?
  - Có nên dùng DTO cho mọi request nhỏ không?

### Câu 31. RBAC trong dự án hoạt động thế nào?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Dự án có ba vai trò nên rất dễ bị hỏi.
- Gợi ý trả lời:
  - RBAC là phân quyền theo vai trò: Khách hàng, Nhà cung cấp, Admin.
  - User liên kết với bảng vai trò.
  - Route provider được bảo vệ bởi `EnsureUserIsProvider`, route admin bởi `EnsureUserIsAdmin`.
  - Dashboard chung redirect theo vai trò.
- Câu hỏi bẫy/hỏi tiếp:
  - Authentication khác authorization thế nào?
  - Nếu chỉ ẩn menu admin ở frontend thì đã an toàn chưa?

### Câu 32. Middleware `auth`, `verified`, `EnsureUserIsProvider`, `EnsureUserIsAdmin` khác nhau thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra phân quyền route.
- Gợi ý trả lời:
  - `auth` yêu cầu đăng nhập.
  - `verified` yêu cầu email đã xác minh.
  - `EnsureUserIsProvider` yêu cầu role Nhà cung cấp.
  - `EnsureUserIsAdmin` yêu cầu role Admin.
- Câu hỏi bẫy/hỏi tiếp:
  - Customer có đi vào provider route được không?
  - Provider có tự duyệt dịch vụ của mình như admin được không?

### Câu 33. Nếu provider cố sửa dịch vụ của provider khác thì backend phải kiểm tra ở đâu?

- Mức độ: Bẫy.
- Vì sao giảng viên hỏi: Kiểm tra authorization theo ownership, không chỉ role.
- Gợi ý trả lời:
  - Không chỉ kiểm tra user là provider, còn phải kiểm tra dịch vụ thuộc provider đó.
  - Kiểm tra trong controller/service/policy trước khi update/delete.
  - Frontend ẩn nút sửa không đủ vì user có thể tự gửi request.
- Câu hỏi bẫy/hỏi tiếp:
  - Role đúng nhưng owner sai thì HTTP status nên là gì?
  - Policy có phù hợp hơn middleware không?

### Câu 34. Form Request validation quan trọng ở đâu trong luồng booking?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Booking là input quan trọng.
- Gợi ý trả lời:
  - Form Request như `CreateBookingRequest` kiểm tra dữ liệu trước khi service tạo đơn.
  - Nó đảm bảo dữ liệu bắt buộc, kiểu dữ liệu, ngày giờ, service id hợp lệ.
  - Backend validation bắt buộc vì frontend validation có thể bị bypass.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu tắt JavaScript thì backend có còn validate không?
  - Validation có thay thế business rule không?

### Câu 35. Business rule khác validation thường ở điểm nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra phân biệt input hợp lệ và nghiệp vụ hợp lệ.
- Gợi ý trả lời:
  - Validation kiểm tra hình thức dữ liệu: required, string, date, exists.
  - Business rule kiểm tra logic nghiệp vụ: provider có rảnh không, booking có được hủy không, chỉ booking hoàn thành mới review.
  - Business rule thường nằm trong service/domain logic.
- Câu hỏi bẫy/hỏi tiếp:
  - Ngày đúng định dạng nhưng nằm trong quá khứ thì là validation hay business rule?
  - Ai chịu trách nhiệm chống double-booking?

### Câu 36. Eloquent Model đại diện cho gì?

- Mức độ: Dễ.
- Vì sao giảng viên hỏi: Kiểm tra ORM cơ bản.
- Gợi ý trả lời:
  - Model đại diện bảng database và định nghĩa relationship.
  - Ví dụ `User` đại diện `nguoi_dung`, `DichVu` đại diện `dich_vu`, `DonDatLich` đại diện `don_dat_lich`.
  - Model giúp thao tác DB bằng object thay vì SQL thủ công.
- Câu hỏi bẫy/hỏi tiếp:
  - Model có nên chứa toàn bộ nghiệp vụ booking không?
  - Eloquent có chống SQL injection không?

### Câu 37. Quan hệ chính giữa các bảng trong dự án là gì?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: ERD là phần bảo vệ thường gặp.
- Gợi ý trả lời:
  - Một vai trò có nhiều user.
  - Provider có hồ sơ provider và nhiều dịch vụ.
  - Dịch vụ thuộc danh mục, provider và địa điểm.
  - Booking liên kết customer, provider và service.
  - Review thuộc booking/customer/provider.
  - Notification thuộc user.
  - Conversation có customer/provider, message thuộc conversation và sender.
- Câu hỏi bẫy/hỏi tiếp:
  - Vì sao review nên gắn với booking?
  - Vì sao `ho_so_nha_cung_cap` có quan hệ 1-1 với user?

### Câu 38. N+1 query là gì và dự án có thể gặp ở đâu?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra hiệu năng backend.
- Gợi ý trả lời:
  - N+1 xảy ra khi load danh sách N bản ghi rồi mỗi bản ghi lại query quan hệ riêng.
  - Có thể gặp ở danh sách dịch vụ kèm provider/category, booking kèm customer/provider/service, dashboard thống kê.
  - Cách xử lý là eager loading bằng `with()` hoặc query tổng hợp.
- Câu hỏi bẫy/hỏi tiếp:
  - Làm sao chứng minh không bị N+1?
  - Eager load quá nhiều có hại không?

### Câu 39. Transaction nên dùng trong những nghiệp vụ nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra tính toàn vẹn dữ liệu.
- Gợi ý trả lời:
  - Dùng khi nhiều thao tác DB phải thành công/thất bại cùng nhau.
  - Ví dụ tạo booking kèm giao dịch đặt cọc/thông báo, hoàn tiền, rút tiền.
  - Nếu một bước lỗi thì rollback để tránh dữ liệu nửa vời.
- Câu hỏi bẫy/hỏi tiếp:
  - Gửi notification realtime nên nằm trong transaction hay sau commit?
  - Nếu broadcast thành công nhưng DB rollback thì sao?

### Câu 40. Mass assignment là gì và phòng tránh thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Security Laravel rất hay bị hỏi.
- Gợi ý trả lời:
  - Mass assignment là gán hàng loạt input vào model.
  - Nếu không kiểm soát `$fillable`/`$guarded`, user có thể gửi field nhạy cảm như role/status.
  - Phòng tránh bằng Form Request, chỉ lấy field cho phép, cấu hình fillable đúng.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu customer gửi `vai_tro=Admin` khi đăng ký thì sao?
  - Frontend bỏ field role có đủ không?

### Câu 41. CSRF bảo vệ gì trong dự án?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Form POST trong Laravel cần CSRF.
- Gợi ý trả lời:
  - CSRF chống việc site khác lợi dụng session người dùng để gửi request trái phép.
  - Laravel dùng CSRF token cho các request thay đổi dữ liệu.
  - Chat/form POST cũng cần token khi gọi fetch thủ công.
- Câu hỏi bẫy/hỏi tiếp:
  - CSRF khác XSS thế nào?
  - API token có cần CSRF không?

### Câu 42. SQL injection được Laravel phòng tránh thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Security cơ bản.
- Gợi ý trả lời:
  - Eloquent/Query Builder dùng parameter binding nên an toàn hơn nối chuỗi SQL.
  - Vẫn cần cẩn thận nếu dùng raw query, orderBy động, filter từ user input.
  - Validation và whitelist field sort/filter là cần thiết.
- Câu hỏi bẫy/hỏi tiếp:
  - Eloquent có đảm bảo an toàn 100% nếu dùng `DB::raw` không?
  - Search keyword nên xử lý thế nào?

### Câu 43. Notification flow backend hoạt động thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Dự án có thông báo realtime.
- Gợi ý trả lời:
  - Khi booking/review/service thay đổi, service tạo bản ghi `thong_bao`.
  - `NotificationService` quản lý tạo, đọc, đọc tất cả.
  - Event realtime có thể broadcast đến private user channel để cập nhật bell/sidebar.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu user offline thì notification có mất không?
  - Vì sao vẫn cần lưu DB dù có realtime?

### Câu 44. Chat realtime cần bảo vệ channel thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Chat là dữ liệu riêng tư.
- Gợi ý trả lời:
  - Channel conversation phải là private channel.
  - `routes/channels.php` kiểm tra user có thuộc conversation không.
  - Chỉ customer/provider liên quan mới subscribe được.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu user đoán được conversation id thì có nghe được không?
  - Message gửi qua realtime có cần lưu DB không?

### Câu 45. Booking lifecycle nên có những trạng thái nào?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Đây là nghiệp vụ lõi.
- Gợi ý trả lời:
  - Trạng thái thường gồm chờ xác nhận, đã xác nhận, bị từ chối, đã hủy, hoàn thành.
  - Payment có trạng thái riêng như chưa thanh toán, đặt cọc, đã thanh toán, hoàn tiền.
  - Service layer phải kiểm soát chuyển trạng thái hợp lệ.
- Câu hỏi bẫy/hỏi tiếp:
  - Booking đã hoàn thành có được hủy không?
  - Provider từ chối thì tiền cọc xử lý thế nào?

### Câu 46. Review chỉ nên được tạo khi nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra ràng buộc nghiệp vụ.
- Gợi ý trả lời:
  - Customer chỉ nên review booking đã hoàn thành.
  - Mỗi booking chỉ nên có một review để tránh spam.
  - Review liên kết booking, customer và provider để đảm bảo tính xác thực.
- Câu hỏi bẫy/hỏi tiếp:
  - Customer chưa đặt dịch vụ có review được không?
  - Provider có được tự review mình không?

### Câu 47. Admin duyệt dịch vụ để làm gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra vai trò admin trong marketplace.
- Gợi ý trả lời:
  - Admin kiểm soát chất lượng dịch vụ trước khi hiển thị công khai.
  - Giảm rủi ro dịch vụ sai, lừa đảo, vi phạm chính sách.
  - Dịch vụ provider tạo có trạng thái chờ duyệt/duyệt/từ chối.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu không có admin duyệt thì rủi ro gì?
  - Provider có nên tự publish ngay không?

### Câu 48. Dashboard backend lấy dữ liệu như thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Dashboard liên quan thống kê/query.
- Gợi ý trả lời:
  - Dashboard tổng hợp số liệu từ booking, service, user, review, giao dịch.
  - Nên đặt logic thống kê trong `DashboardService` hoặc repository thay vì controller.
  - Cần tối ưu query, count, sum, groupBy, tránh load quá nhiều bản ghi.
- Câu hỏi bẫy/hỏi tiếp:
  - Dashboard realtime có cần reload toàn trang không?
  - Nếu dữ liệu lớn thì thống kê mỗi request có ổn không?

### Câu 49. Search/filter dịch vụ cần lưu ý gì?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Search là chức năng public quan trọng.
- Gợi ý trả lời:
  - Cần filter theo từ khóa, danh mục, khu vực, giá, trạng thái duyệt/hoạt động.
  - Chỉ hiển thị dịch vụ hợp lệ, đã duyệt.
  - Cần pagination và index phù hợp để tránh chậm.
- Câu hỏi bẫy/hỏi tiếp:
  - Search có nên tìm cả dịch vụ chưa duyệt không?
  - Keyword người dùng nhập có thể gây SQL injection không?

### Câu 50. Email verification có ý nghĩa gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Route dùng `verified`.
- Gợi ý trả lời:
  - Xác minh email giúp đảm bảo tài khoản dùng email thật.
  - Các khu vực cần đăng nhập được bảo vệ bởi `auth` và `verified`.
  - Giảm spam và hỗ trợ reset password an toàn hơn.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu user chưa verify thì có vào dashboard được không?
  - Email verification có thay thế KYC provider không?

## 3. Nhóm Frontend Vue/Inertia

### Câu 51. `resources/js/app.ts` có vai trò gì?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Đây là entry point frontend.
- Gợi ý trả lời:
  - Khởi tạo Inertia Vue app.
  - Resolve page từ `resources/js/pages/**/*.vue`.
  - Cấu hình Echo/Reverb, GSAP animation, theme và sync auth giữa tab.
  - Mount Vue app vào DOM.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu page name từ controller sai thì lỗi gì?
  - `app.ts` có phải nơi viết logic booking không?

### Câu 52. Page, Layout và Component khác nhau thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra tổ chức frontend.
- Gợi ý trả lời:
  - Page là màn hình ứng với route Inertia, ví dụ dashboard, service listing.
  - Layout là khung giao diện dùng chung theo khu vực như admin/customer/provider.
  - Component là UI tái sử dụng như notification bell, header, form input, toast.
- Câu hỏi bẫy/hỏi tiếp:
  - Có nên để toàn bộ UI trong một page không?
  - Layout có quyết định quyền truy cập thật sự không?

### Câu 53. Dữ liệu từ Laravel sang Vue đi qua đâu?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Kiểm tra hiểu Inertia.
- Gợi ý trả lời:
  - Controller Laravel trả Inertia response kèm props.
  - Vue page nhận props bằng `defineProps` hoặc `usePage`.
  - Shared props như auth user, unread notifications có thể dùng trong layout/component.
- Câu hỏi bẫy/hỏi tiếp:
  - Props có phải API REST không?
  - Nếu props thiếu field thì TypeScript có phát hiện hết không?

### Câu 54. Frontend validation có đủ bảo mật không?

- Mức độ: Bẫy.
- Vì sao giảng viên hỏi: Đây là lỗi sinh viên hay trả lời sai.
- Gợi ý trả lời:
  - Không đủ.
  - Frontend validation chỉ cải thiện UX.
  - Backend Form Request/service rule mới là lớp bảo vệ thật vì request có thể bị gửi bằng Postman/curl/devtools.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu xóa thuộc tính `required` trong HTML thì sao?
  - Error từ backend hiển thị trên Vue như thế nào?

### Câu 55. Vì sao không được xem việc ẩn nút trên Vue là bảo mật?

- Mức độ: Bẫy.
- Vì sao giảng viên hỏi: Kiểm tra tư duy security.
- Gợi ý trả lời:
  - UI chỉ làm người dùng bình thường không thấy nút.
  - Người dùng vẫn có thể gửi request trực tiếp tới endpoint.
  - Backend phải kiểm tra middleware, policy/ownership và validation.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu customer tự gọi `/admin/users` thì chặn ở đâu?
  - Nếu provider sửa service người khác thì chặn ở đâu?

### Câu 56. `ref`, `computed`, `watch` dùng trong Vue để làm gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra Composition API.
- Gợi ý trả lời:
  - `ref` tạo state reactive đơn giản.
  - `computed` tạo giá trị dẫn xuất từ state.
  - `watch` theo dõi thay đổi để chạy side effect như subscribe/unsubscribe channel.
- Câu hỏi bẫy/hỏi tiếp:
  - Khi nào computed tốt hơn method?
  - Watch có thể gây memory leak không?

### Câu 57. Composable trong frontend có lợi gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra tái sử dụng logic.
- Gợi ý trả lời:
  - Composable gom logic dùng lại như appearance, current URL, realtime user channel.
  - Giúp component/page gọn hơn.
  - Có thể chia sẻ state hoặc behavior giữa layout/component.
- Câu hỏi bẫy/hỏi tiếp:
  - Composable có nên chứa toàn bộ nghiệp vụ backend không?
  - Nếu composable subscribe websocket thì phải cleanup thế nào?

### Câu 58. Notification bell realtime trên frontend nên hoạt động thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Liên quan realtime + UX.
- Gợi ý trả lời:
  - Component/layout lấy unread count từ shared state/composable.
  - Khi nhận event `NotificationCreated`, tăng unread và thêm notification mới.
  - Khi mark read, cập nhật read state và count.
  - Vẫn có endpoint lấy recent notifications để load dữ liệu ban đầu.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu user mở hai tab thì count có đồng bộ không?
  - Nếu mất websocket thì còn xem notification được không?

### Câu 59. Chat frontend cần tránh lỗi gì khi realtime?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Chat dễ lỗi duplicate/memory leak.
- Gợi ý trả lời:
  - Không append trùng message nếu vừa gửi xong vừa nhận event.
  - Khi đổi conversation phải leave channel cũ.
  - Khi unmount page phải cleanup listener.
  - Chỉ subscribe conversation đang active.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu không leave channel cũ thì điều gì xảy ra?
  - Nếu event đến trước khi fetch message xong thì xử lý sao?

### Câu 60. Type frontend nên khớp backend như thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: TypeScript chỉ hữu ích khi type đúng.
- Gợi ý trả lời:
  - Interface/type ở frontend phải phản ánh shape props/API backend trả về.
  - Ví dụ notification có `id`, `title`, `body`, `read`, `date`; message có sender, content, created_at.
  - Nếu backend đổi tên field thì frontend type/page phải cập nhật.
- Câu hỏi bẫy/hỏi tiếp:
  - TypeScript có tự biết migration DB không?
  - Dùng `any` để hết lỗi có tốt không?

### Câu 61. Responsive design trong Tailwind kiểm tra như thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Đồ án thường bị kiểm tra mobile.
- Gợi ý trả lời:
  - Dùng breakpoint `sm`, `md`, `lg`, `xl` để thay đổi layout theo kích thước.
  - Cần kiểm tra header/sidebar, dashboard, form booking, bảng admin/provider, chat trên mobile.
  - UI phải không tràn ngang, nút đủ dễ bấm, text dễ đọc.
- Câu hỏi bẫy/hỏi tiếp:
  - Responsive có chỉ là co màn hình không?
  - Bảng nhiều cột trên mobile xử lý sao?

### Câu 62. Accessibility cơ bản cần chú ý gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: UI/UX không chỉ là đẹp.
- Gợi ý trả lời:
  - Form cần label rõ, focus state, keyboard navigation.
  - Button/icon cần aria-label nếu không có text.
  - Màu sắc phải đủ tương phản.
  - Modal/dropdown nên đóng được bằng click ngoài hoặc keyboard nếu có.
- Câu hỏi bẫy/hỏi tiếp:
  - Icon button không có text thì screen reader đọc gì?
  - Animation có ảnh hưởng người nhạy cảm chuyển động không?

### Câu 63. GSAP trong dự án dùng cho mục đích gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Có dependency animation thì cần giải thích được.
- Gợi ý trả lời:
  - GSAP hỗ trợ animation và page transition.
  - `ScrollTrigger` dùng cho hiệu ứng khi cuộn.
  - Cần tôn trọng `prefers-reduced-motion` để giảm animation cho người dùng nhạy cảm.
- Câu hỏi bẫy/hỏi tiếp:
  - Animation có được ưu tiên hơn hiệu năng không?
  - Nếu JS lỗi thì animation ảnh hưởng app thế nào?

### Câu 64. Leaflet/vue-leaflet dùng để làm gì trong website dịch vụ địa phương?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Dịch vụ địa phương thường liên quan bản đồ.
- Gợi ý trả lời:
  - Leaflet hiển thị bản đồ, vị trí dịch vụ/provider/khu vực.
  - Có thể dùng tọa độ hoặc địa chỉ hành chính để hỗ trợ tìm dịch vụ gần khu vực.
  - Cần cân nhắc quyền riêng tư vị trí và dữ liệu chính xác.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu không có tọa độ thì map hiển thị gì?
  - Tìm gần nhất nên xử lý ở frontend hay backend?

### Câu 65. Inertia form submit khác fetch/axios thủ công thế nào?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Dự án có cả Inertia và fetch/axios.
- Gợi ý trả lời:
  - Inertia form phù hợp submit page/form và nhận validation errors/redirect.
  - Fetch/axios phù hợp API nhỏ như load JSON, chat message, recent notifications.
  - Cần thống nhất cách xử lý loading/error để UX tốt.
- Câu hỏi bẫy/hỏi tiếp:
  - Dùng fetch thủ công có tự nhận Inertia validation errors không?
  - Khi nào nên dùng router.reload?

## 4. Nhóm toàn bộ website và nghiệp vụ

### Câu 66. Website này giải quyết vấn đề gì?

- Mức độ: Dễ nhưng cần trả lời hay.
- Vì sao giảng viên hỏi: Kiểm tra hiểu bài toán, không chỉ code.
- Gợi ý trả lời:
  - Website kết nối khách hàng cần dịch vụ địa phương với nhà cung cấp dịch vụ.
  - Khách hàng có thể tìm kiếm, xem chi tiết, đặt lịch, thanh toán/đặt cọc, chat, đánh giá.
  - Provider quản lý hồ sơ, dịch vụ, booking, tài chính.
  - Admin quản lý người dùng, duyệt dịch vụ, thống kê và kiểm soát chất lượng.
- Câu hỏi bẫy/hỏi tiếp:
  - Điểm khác marketplace này với website giới thiệu dịch vụ đơn thuần là gì?
  - Ai là người tạo giá trị chính trong hệ thống?

### Câu 67. Ba vai trò customer, provider, admin khác nhau thế nào?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: RBAC và nghiệp vụ lõi.
- Gợi ý trả lời:
  - Customer tìm, đặt, thanh toán, review, nhận thông báo.
  - Provider đăng dịch vụ, xử lý booking, phản hồi review, theo dõi doanh thu.
  - Admin duyệt dịch vụ, quản lý user, thống kê, xử lý tài chính/review.
- Câu hỏi bẫy/hỏi tiếp:
  - Provider có thể là customer không?
  - Admin có nên tự can thiệp booking không?

### Câu 68. Luồng đặt lịch đầy đủ diễn ra như thế nào?

- Mức độ: Rất quan trọng.
- Vì sao giảng viên hỏi: Đây là luồng nghiệp vụ trung tâm.
- Gợi ý trả lời:
  - Customer xem danh sách/chi tiết dịch vụ.
  - Customer gửi form booking.
  - Backend validate request và service tạo `don_dat_lich`.
  - Provider nhận thông báo và xác nhận/từ chối.
  - Sau khi dịch vụ hoàn thành, customer có thể đánh giá.
  - Dashboard/notification/chat hỗ trợ theo dõi quá trình.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu provider không phản hồi thì xử lý thế nào?
  - Booking tạo xong trạng thái ban đầu là gì?

### Câu 69. Nếu provider sửa giá sau khi customer đã đặt lịch thì đơn tính giá nào?

- Mức độ: Bẫy khó.
- Vì sao giảng viên hỏi: Kiểm tra tư duy dữ liệu lịch sử.
- Gợi ý trả lời:
  - Booking nên lưu snapshot giá tại thời điểm đặt.
  - Nếu chỉ tham chiếu giá hiện tại của dịch vụ, đơn cũ sẽ bị sai khi provider sửa giá.
  - Đây là lý do booking/payment cần lưu số tiền thực tế.
- Câu hỏi bẫy/hỏi tiếp:
  - Dữ liệu lịch sử nên lưu ở bảng booking hay transaction?
  - Nếu admin đổi phí nền tảng thì booking cũ có đổi không?

### Câu 70. Nếu 2 khách đặt cùng khung giờ thì hệ thống ngăn thế nào?

- Mức độ: Bẫy khó.
- Vì sao giảng viên hỏi: Kiểm tra concurrency và business rule.
- Gợi ý trả lời:
  - Cần kiểm tra lịch provider/service trước khi tạo booking.
  - Nên dùng transaction/locking hoặc constraint phù hợp để tránh race condition.
  - Chỉ validation frontend không đủ vì hai request có thể đến cùng lúc.
- Câu hỏi bẫy/hỏi tiếp:
  - Kiểm tra trùng lịch ở frontend có đủ không?
  - PostgreSQL constraint có giúp được không?

### Câu 71. Luồng provider đăng dịch vụ và admin duyệt diễn ra thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra marketplace quality control.
- Gợi ý trả lời:
  - Provider tạo dịch vụ qua provider service controller/service.
  - Dịch vụ lưu trạng thái chờ duyệt.
  - Admin xem danh sách và approve/reject.
  - Chỉ dịch vụ hợp lệ/đã duyệt nên xuất hiện public.
- Câu hỏi bẫy/hỏi tiếp:
  - Provider có sửa dịch vụ đã duyệt không?
  - Khi sửa lại có cần duyệt lại không?

### Câu 72. Luồng notification trong toàn hệ thống phục vụ gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Notification liên kết nhiều module.
- Gợi ý trả lời:
  - Thông báo giúp user biết booking mới, thay đổi trạng thái, tin nhắn, admin action.
  - Backend lưu `thong_bao` để không mất thông tin khi user offline.
  - Realtime chỉ giúp cập nhật tức thời trên UI.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu realtime lỗi thì thông báo có còn trong DB không?
  - Đọc thông báo ở một tab có cập nhật tab khác không?

### Câu 73. Chat giữa customer và provider nên gắn với gì?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra dữ liệu hội thoại.
- Gợi ý trả lời:
  - Conversation nên có customer, provider, có thể gắn booking nếu chat theo đơn.
  - Message lưu sender, content, read state, created_at.
  - Realtime giúp nhận tin mới ngay nhưng DB vẫn là nguồn lưu trữ.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu provider bị khóa thì chat cũ xử lý sao?
  - Có cần soft delete message không?

### Câu 74. Dashboard customer/provider/admin khác nhau thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: Kiểm tra UI theo role và data theo role.
- Gợi ý trả lời:
  - Customer dashboard hiển thị booking, favorite, notification, lịch gần đây.
  - Provider dashboard hiển thị booking chờ xác nhận, dịch vụ, doanh thu, review.
  - Admin dashboard hiển thị tổng user, service, booking, doanh thu/thống kê hệ thống.
- Câu hỏi bẫy/hỏi tiếp:
  - Dữ liệu dashboard có nên lấy tất cả rồi lọc frontend không?
  - Provider có được thấy doanh thu provider khác không?

### Câu 76. Nếu dữ liệu tăng lớn, phần nào dễ nghẽn?

- Mức độ: Khó.
- Vì sao giảng viên hỏi: Kiểm tra hiệu năng và scalability.
- Gợi ý trả lời:
  - Search/filter dịch vụ, dashboard thống kê, notification/chat realtime, booking conflict check.
  - Cần index, pagination, eager loading, cache/thống kê định kỳ, queue cho tác vụ nặng.
  - Database và websocket server cần monitor riêng.
- Câu hỏi bẫy/hỏi tiếp:
  - Cache dashboard có làm dữ liệu chậm cập nhật không?
  - Websocket có thay thế database query không?

### Câu 77. AI planner nếu được hỏi thì nên trình bày thế nào?

- Mức độ: Trung bình.
- Vì sao giảng viên hỏi: AI là điểm dễ bị hỏi sâu.
- Gợi ý trả lời:
  - AI planner hỗ trợ gợi ý lịch trình/dịch vụ theo nhu cầu người dùng.
  - Backend cần validate prompt/input, throttle request, gọi service AI, trả kết quả cho frontend.
  - Không nên nói AI quyết định nghiệp vụ lõi; nó chỉ hỗ trợ gợi ý.
- Câu hỏi bẫy/hỏi tiếp:
  - Nếu AI trả sai thì ai chịu trách nhiệm?
  - Có lưu prompt/dữ liệu cá nhân không?

## 5. Bộ câu hỏi bẫy nhanh

1. Dự án dùng Inertia thì có phải frontend-backend tách rời hoàn toàn không?
   - Trả lời: Không. Đây là monolith Laravel hiện đại, Vue render page qua Inertia; không phải SPA + REST API tách riêng hoàn toàn.

2. Nếu JavaScript bị tắt thì website còn hoạt động đầy đủ không?
   - Trả lời: Không đầy đủ, vì UI chính dùng Vue/Inertia. Backend vẫn có route nhưng trải nghiệm phụ thuộc JS.

3. Ẩn nút admin trên frontend có đủ bảo mật không?
   - Trả lời: Không. Phải chặn bằng middleware/policy/backend authorization.

4. Validation frontend có thay thế Form Request không?
   - Trả lời: Không. Frontend chỉ hỗ trợ UX; backend mới bắt buộc.

5. DTO có thay thế validation không?
   - Trả lời: Không. DTO đóng gói dữ liệu đã hợp lệ, validation kiểm tra dữ liệu.

6. Repository có luôn luôn tốt không?
   - Trả lời: Không. Hữu ích cho query phức tạp/tách nguồn dữ liệu; CRUD quá đơn giản có thể overengineering.

7. Websocket có thay thế database không?
   - Trả lời: Không. Websocket truyền cập nhật tức thời; database vẫn lưu trạng thái bền vững.

8. Nếu user offline thì notification realtime có mất không?
   - Trả lời: Realtime event có thể không nhận, nhưng notification vẫn phải lưu trong DB để xem lại.

9. Nếu provider bị khóa thì service của họ có nên public không?
   - Trả lời: Không nên; search/listing nên filter theo trạng thái provider/service.

10. Nếu booking hoàn thành rồi customer hủy thì có hợp lý không?
    - Trả lời: Không. Service layer phải kiểm soát transition hợp lệ.

11. Nếu dùng Eloquent thì chắc chắn không SQL injection đúng không?
    - Trả lời: Không tuyệt đối. Eloquent an toàn với binding, nhưng raw query/dynamic sort vẫn có rủi ro.

12. Nếu ERD trong báo cáo khác migration thì cái nào đúng?
    - Trả lời: Migration là source tạo database thật, ERD phải sửa để khớp migration.

13. Nếu app chạy được local thì có chắc production chạy tốt không?
    - Trả lời: Không. Production cần cấu hình env, cache, queue, websocket, permission, security, backup.

14. Nếu `npm run build` lỗi vì thiếu Vite thì lỗi do code hay môi trường?
    - Trả lời: Thường do chưa `npm install`/thiếu `node_modules`, cần phân biệt lỗi dependency và lỗi source.

15. Nếu CloudBeaver không thấy database thì dữ liệu có mất không?
    - Trả lời: Chưa chắc. Có thể chưa tạo connection đúng host `postgres`; dữ liệu nằm trong volume PostgreSQL.

## 6. Trả lời yếu vs trả lời tốt

### Chủ đề: Vì sao chọn Laravel?

- Trả lời yếu: Vì Laravel dễ dùng và phổ biến.
- Trả lời tốt: Vì dự án cần routing, middleware, auth, validation, ORM, migration, service container, testing và tổ chức MVC rõ ràng. Laravel giúp triển khai marketplace nhiều vai trò nhanh hơn PHP thuần và dễ bảo trì hơn.

### Chủ đề: Vì sao chọn Inertia?

- Trả lời yếu: Vì Inertia kết nối Laravel với Vue.
- Trả lời tốt: Vì Inertia cho phép giữ Laravel routing/session auth/validation ở backend, đồng thời dùng Vue để xây UI tương tác. Cách này phù hợp monolith có nhiều dashboard/form và không cần xây API REST riêng cho mọi màn hình.

### Chủ đề: Vì sao dùng Service Layer?

- Trả lời yếu: Để code gọn hơn.
- Trả lời tốt: Service Layer gom nghiệp vụ như booking lifecycle, notification, service CRUD, dashboard ra khỏi controller. Nhờ đó controller mỏng, logic dễ test, dễ tái sử dụng và ít trùng lặp.

### Chủ đề: Vì sao backend authorization quan trọng hơn frontend hiding?

- Trả lời yếu: Vì backend an toàn hơn.
- Trả lời tốt: Frontend chỉ quyết định hiển thị UI. Người dùng có thể gửi request trực tiếp bằng devtools/Postman. Vì vậy quyền thật phải kiểm tra ở backend bằng middleware, policy/ownership và validation.

### Chủ đề: Migration và ERD

- Trả lời yếu: ERD là sơ đồ database.
- Trả lời tốt: ERD là tài liệu trực quan, còn migration là code tạo schema thật. Khi bảo vệ phải đảm bảo ERD, migration và Eloquent relationship khớp nhau; nếu không, migration là nguồn sự thật của database.

## 7. Những điểm không nên nói quá khi bảo vệ

- Không nói frontend bảo mật quyền truy cập; backend mới quyết định quyền.
- Không nói dự án là REST API + SPA tách rời nếu phần chính dùng Inertia.
- Không nói validation frontend là đủ.
- Không nói repository luôn luôn tốt; phải nói được tradeoff.
- Không nói realtime thay thế lưu database.
- Không nói AI planner là quyết định nghiệp vụ chính; chỉ nên nói là hỗ trợ gợi ý.
- Không nói payment production-ready nếu chưa tích hợp cổng thanh toán thật, webhook, audit log và test đầy đủ.
- Không nói ERD đúng nếu chưa đối chiếu migration.
- Không nói Docker là nơi lưu dữ liệu; dữ liệu PostgreSQL nằm trong volume.
- Không nói chạy được local là đủ để deploy production.

## 8. Checklist học thuộc trước khi bảo vệ

- [ ] Nói được toàn bộ stack: PHP, Laravel, Fortify, Inertia, Vue, TypeScript, Vite, Tailwind, PostgreSQL, Docker, Reverb/Echo.
- [ ] Nói được request flow: Browser → Inertia/Vue → Laravel route → middleware → controller → request → service → repository/model → database → response.
- [ ] Nói được 3 role và quyền chính: Khách hàng, Nhà cung cấp, Admin.
- [ ] Nói được bảng chính và quan hệ: `nguoi_dung`, `vai_tro_nguoi_dung`, `dich_vu`, `don_dat_lich`, `danh_gia`, `thong_bao`, `conversations`, `messages`.
- [ ] Nói được luồng đặt lịch đầy đủ.
- [ ] Nói được luồng provider tạo dịch vụ và admin duyệt.
- [ ] Nói được vì sao backend validation/authorization là bắt buộc.
- [ ] Nói được Service Layer, Repository Pattern, DTO dùng để làm gì và tradeoff.
- [ ] Nói được Inertia khác REST API SPA riêng thế nào.
- [ ] Nói được cách xem database trong CloudBeaver và vì sao host là `postgres`.
- [ ] Chuẩn bị câu trả lời cho câu hỏi bẫy về double-booking, sửa giá sau khi đặt, N+1, realtime lỗi, frontend hiding.

## 9. Câu hỏi rapid-fire để tự luyện

1. Laravel chạy ở đâu?
2. Vue chạy ở đâu?
3. Inertia có phải API REST không?
4. Fortify có cung cấp UI không?
5. Middleware chạy trước hay sau controller?
6. Form Request kiểm tra gì?
7. Service Layer khác controller thế nào?
8. Repository khác model thế nào?
9. DTO khác array thế nào?
10. Migration khác seeder thế nào?
11. Eloquent relationship dùng để làm gì?
12. N+1 query là gì?
13. CSRF chống kiểu tấn công nào?
14. XSS khác SQL injection thế nào?
15. Provider route được bảo vệ bằng gì?
16. Admin có role tên gì trong hệ thống?
17. Booking liên kết những đối tượng nào?
18. Review nên gắn với bảng nào?
19. Notification vì sao phải lưu DB?
20. Websocket giải quyết vấn đề gì?
21. Vite dùng khi dev và build thế nào?
22. TypeScript có kiểm tra dữ liệu runtime không?
23. Tailwind mobile-first là gì?
24. CloudBeaver kết nối host nào trong Docker?
25. Dữ liệu PostgreSQL nằm ở đâu?
26. Nếu queue/websocket chết thì phần nào bị ảnh hưởng?
27. Nếu frontend bị bypass thì backend chặn ở đâu?
28. Nếu user đổi role giữa session thì cần lưu ý gì?
29. Nếu service bị xóa thì booking cũ có mất ý nghĩa không?
30. Nếu mở mobile app thì cần thêm gì?

## 10. Câu trả lời mẫu tổng hợp khi giảng viên yêu cầu mô tả toàn bộ dự án

Dự án là website marketplace dịch vụ địa phương, kết nối khách hàng với nhà cung cấp và có admin kiểm soát hệ thống. Backend dùng PHP 8.2 và Laravel 12 để quản lý route, middleware, authentication bằng Fortify, validation, service layer, repository, Eloquent model và migrations trên PostgreSQL. Frontend dùng Vue 3, TypeScript, Inertia, Vite và Tailwind CSS để xây page, layout, component responsive cho customer, provider và admin. Luồng chính là khách hàng tìm dịch vụ, xem chi tiết, đặt lịch, thanh toán/đặt cọc, chat/nhận thông báo, provider xác nhận và sau khi hoàn thành customer đánh giá. Admin quản lý user, duyệt dịch vụ, xem thống kê và kiểm soát chất lượng. Điểm quan trọng của kiến trúc là backend vẫn chịu trách nhiệm bảo mật, validation và nghiệp vụ; frontend tập trung vào trải nghiệm người dùng. Database được định nghĩa bằng migration, ERD/báo cáo phải đối chiếu với source thật để tránh mô tả sai hệ thống.
