# Next.js Migration Report

Ngày audit/migration: 2026-08-12.

## 1. Original stack và inventory

Project gốc là Laravel 12.53 + Inertia Laravel 2.0 + Vue 3.5 + Vite 7 + TypeScript strict + Tailwind CSS 4, PostgreSQL, Fortify, Reverb và Laravel Echo. Deployment cũ dùng Docker PHP-FPM/Nginx/PostgreSQL/Reverb với filesystem local và các tiến trình sống lâu.

Inventory đã kiểm tra:

- 124 route definitions, 105 URI patterns duy nhất, gồm 68 route có thể GET và 58 mutation.
- 53 Inertia page targets khớp 53 file `resources/js/pages/**/*.vue` và 53 entry trong registry Next.
- 145 Vue component SFC, 13 layout SFC, 34 controller, 15 model, 17 migration và 13 PHP test files.
- 35 controller/service data flows tới Eloquent/PostgreSQL; AI gọi từ Laravel server; realtime dùng Reverb; upload ghi qua Laravel filesystem.
- `fene/` là prototype React/Vite AI Studio độc lập, dùng mock data, không được root build tham chiếu và không phải ứng dụng production.
- Repository hiện tại không có thư mục `.git`, vì vậy không thể lấy `git status` hoặc xác định file nào từng được commit.

## 2. Route inventory

Next App Router dùng `app/[[...path]]/page.tsx` để giữ nguyên mọi public URL. GET page được Next lấy dưới dạng Inertia JSON; mutation/XHR đi qua `proxy.ts` tới Laravel. `page` là query pagination ngầm cho các danh sách phân trang.

### Public và authentication

| Method | URL | Handler/page | Query/params | Guard |
|---|---|---|---|---|
| GET | `/` | `HomeController@index` → `Welcome.vue` | — | Public |
| GET | `/services` | `ServiceController@index` → `services/Index.vue` | `q`, `category` | Public |
| GET | `/services/{id}` | `ServiceController@show` → `services/Show.vue` | `id` | Public |
| GET | `/search` | `SearchController@index` → `search/Index.vue` | `q` | Public |
| GET | `/ai-planner` | `ServiceController@aiPlanner` | — | Public |
| GET | `/about`, `/contact`, `/policy` | Inertia static pages | — | Public |
| GET | `/categories` | `CategoryController@index` | — | Public |
| GET/POST | `/login` | Fortify login view/session | credentials | Guest/mixed |
| POST | `/logout` | Fortify logout | CSRF | Auth |
| GET/POST | `/register` | Fortify registration | customer/provider form | Guest |
| GET/POST | `/forgot-password` | Fortify password link | email | Guest |
| GET | `/reset-password/{token}` | Fortify reset view | `token`, `email` | Guest |
| POST | `/reset-password` | Fortify reset action | token/password | Guest |
| GET | `/email/verify` | `auth/VerifyEmail.vue` | — | Auth |
| GET | `/email/verify/{id}/{hash}` | Fortify signed verify action | signed params | Auth/signed |
| POST | `/email/verification-notification` | Fortify resend | — | Auth/throttled |
| GET/POST | `/user/confirm-password` | Fortify confirm password | password | Auth |
| GET | `/user/confirmed-password-status` | Fortify status | — | Auth |
| POST | `/user/password` | Fortify password update | password fields | Auth |
| PUT | `/user/profile-information` | Fortify profile update | profile fields | Auth |
| POST | `/two-factor-challenge` | Fortify 2FA challenge | code/recovery code | Guest challenge |
| POST/DELETE | `/user/two-factor-authentication` | Fortify enable/disable 2FA | — | Auth/password-confirmed |
| GET | `/user/two-factor-qr-code`, `/user/two-factor-secret-key`, `/user/two-factor-recovery-codes` | Fortify 2FA data | — | Auth/password-confirmed |
| POST | `/user/two-factor-recovery-codes` | Fortify regenerate codes | — | Auth/password-confirmed |
| GET | `/up` | Laravel health endpoint | — | Public |

### Customer và shared authenticated routes

| Method | URL | Handler/feature | Query/params | Guard |
|---|---|---|---|---|
| GET | `/dashboard` | role redirect | — | Auth + verified |
| GET | `/customer/dashboard` | customer overview | — | Customer |
| GET | `/customer/bookings` | booking list | — | Customer |
| POST | `/customer/bookings` | create booking | booking payload | Customer |
| GET | `/customer/bookings/success/{id}` | success page | `id` | Customer |
| GET | `/customer/bookings/{id}` | booking detail | `id` | Customer/owner |
| POST | `/customer/bookings/{id}/cancel` | cancel booking | `ly_do` | Customer/owner |
| GET/POST | `/customer/bookings/{id}/payment[/process]` | checkout/payment | `id`, `action` | Customer/owner |
| GET | `/customer/favorites` | favorites | — | Customer |
| POST | `/customer/favorites/toggle` | toggle favorite | service id | Customer |
| GET | `/customer/profile` | profile form | — | Customer |
| POST/PUT/PATCH | `/customer/profile/update` | profile/avatar/password update | multipart form | Customer |
| GET/POST | `/customer/wallet[/topup]` | wallet + demo top-up | `so_tien` | Customer |
| POST | `/customer/ai-planner/generate`, `/customer/ai-planner/chat` | AI planning/chat | prompt context | Customer/throttled |
| GET/POST | `/customer/reviews/create`, `/customer/reviews` | create/review submit | `booking_id`, review | Customer |
| GET | `/chat`, `/chat/{conversation}` | conversations/messages | conversation id | Auth + verified |
| POST | `/chat/{conversation}/messages`, `/chat/create-or-get` | send/open chat | message/participant | Auth + verified |
| GET | `/notifications`, `/notifications/recent` | role redirect/recent JSON | — | Auth + verified |
| GET | `/customer/notifications[/recent]` | customer notifications | — | Customer |
| POST | `/notifications/{id}/read`, `/notifications/read-all` | read state | id | Auth + verified |

### Provider routes (`/provider`, auth + verified + provider role)

| Method | URL suffix | Handler/feature | Query/params |
|---|---|---|---|
| GET | `/dashboard` | dashboard | — |
| GET, POST/PUT/PATCH | `/profile`, `/profile/update` | profile/bank/avatar/password | multipart form |
| GET/POST | `/services`, `/services/create` | list/create form/submit | `search`, `trang_thai` |
| GET/PUT/DELETE | `/services/{id}`, `/services/{id}/edit` | detail/update/delete | `id` |
| POST | `/services/{id}/toggle-status` | activation | `id` |
| GET | `/bookings`, `/bookings/{id}` | list/detail | `trang_thai`, `search`, `id` |
| POST | `/bookings/{id}/confirm`, `/reject`, `/complete` | booking transitions | `id`, optional `ly_do` |
| GET/PUT | `/availability` | working schedule | `schedule` |
| GET/POST | `/finance`, `/finance/withdraw` | wallet/withdrawal | `so_tien` |
| GET/POST | `/reviews`, `/reviews/{id}/reply` | reviews/reply | `so_sao`, `chua_phan_hoi`, reply |
| GET/POST | `/notifications`, `/notifications/{id}/read`, `/notifications/read-all` | notifications | `id` |

### Admin routes (`/admin`, auth + verified + admin role)

| Method | URL suffix | Handler/feature | Query/params |
|---|---|---|---|
| GET | `/dashboard` | admin overview | — |
| GET, POST/PUT/PATCH | `/profile`, `/profile/update` | profile/avatar/password | multipart form |
| GET/POST | `/users`, `/users/{id}/toggle-status` | user list/account lock | `search`, `role`, `status`, `id` |
| GET/POST | `/services`, `/services/{id}`, `/services/{id}/approve`, `/reject` | moderation | `search`, `status`, `id` |
| GET/POST | `/bookings`, `/bookings/{id}/force-confirm`, `/force-complete`, `/force-reject` | booking management | `search`, `status`, `id` |
| GET/POST | `/settings` | platform configuration | fee settings |
| GET | `/stats` | platform statistics | — |
| GET/POST/PUT/DELETE | `/categories`, `/categories/{id}` | category CRUD | `search`, category form |
| GET/POST | `/finance`, `/finance/{id}/approve`, `/reject` | withdrawal approval | `status`, `id`, `ly_do` |
| GET/DELETE | `/reviews`, `/reviews/{id}` | review moderation | `search`, `so_sao`, `id` |

### Settings và framework routes

| Method | URL | Feature | Guard |
|---|---|---|---|
| GET | `/settings` | redirect to profile | Auth |
| GET/PATCH/DELETE | `/settings/profile` | profile edit/update/delete | Auth; delete verified |
| GET/PUT | `/settings/password` | password edit/update | Auth + verified |
| GET | `/settings/appearance` | theme | Auth + verified |
| GET | `/settings/two-factor` | 2FA settings | Auth + verified |
| POST | `/broadcasting/auth` | private channel auth | Auth/session |
| GET | `/storage/{path}` | backend-hosted upload | path | Public asset |

## 3. Data flow và client/server boundary

```text
Vue form/Link/router
  → same-origin URL
  → Next proxy (mutation, X-Inertia, XHR, storage, broadcast auth)
  → Laravel web middleware (session + CSRF + authorization)
  → controller/service/repository
  → Eloquent/PostgreSQL or AI provider
  → Inertia JSON/redirect
  → Vue/Inertia updates the existing component tree
```

Hard navigation dùng React Server Component để fetch Inertia JSON từ `LARAVEL_API_URL`. Chỉ `src/lib/inertia.ts` đọc backend origin; đây là server-only variable. Reverb key/host là public client config, còn Reverb secret và AI/database credentials chỉ nằm ở Laravel.

Browser APIs đã tìm thấy gồm DOM events, local/session storage, GSAP, Leaflet và WebSocket. Chúng nằm dưới `VueInertiaBridge` có `'use client'`; App Router layout, route loader, metadata, robots và sitemap vẫn là Server Components/server modules.

## 4. Vercel compatibility audit

| Hạng mục | Kết quả |
|---|---|
| Next build/server | Tương thích Vercel; không custom Node server |
| Laravel PHP-FPM | Không chạy trong Vercel deployment này; giữ host riêng |
| PostgreSQL | Giữ nguyên; backend quản lý connection/pooling |
| Session/cache | Database-backed; không dùng RAM của Vercel |
| Upload local | Phải nằm ở backend persistent host; khuyến nghị S3/R2 khi scale |
| Reverb WebSocket | Chạy ngoài Vercel, frontend kết nối WSS trực tiếp |
| Queue/scheduler | Chạy worker/cron ngoài Vercel |
| AI calls | Giữ server-side trong Laravel, keys không lộ client |
| Prototype `fene/` | Bị loại khỏi Vercel upload/build |

`.vercelignore` chỉ gửi phần Next cần thiết. Không thêm `vercel.json` vì Vercel đã nhận Next.js, `npm ci` và `npm run build` đúng theo mặc định.

## 5. Migration decisions và modules

- Cài Next.js 16.3.0 stable, React 19.2.8, App Router và TypeScript strict.
- Thêm Next route shell: `app/layout.tsx`, optional catch-all, loading/error/not-found, robots và sitemap.
- Thêm server-only Inertia loader, external rewrite proxy theo convention `proxy.ts` của Next 16 và Vue loader integration.
- Giữ 53 Vue pages, 145 shared/page components, 13 layouts, Tailwind tokens, animations và URLs để bảo toàn UI/UX và behavior.
- Font Plus Jakarta Sans/Playfair Display chuyển sang `next/font`, vẫn giữ fallback cũ.
- Robots/sitemap chuyển sang Metadata Routes; file `public/robots.txt` cũ bị bỏ vì đã che route robots mới trong production server.
- Cấu hình remote images cho Picsum, Unsplash, Pravatar, UI Avatars và OpenStreetMap.
- npm vẫn là package manager duy nhất; Vite-only packages bị bỏ khỏi root dependency graph. Axios không còn được app import trực tiếp.
- Biến Gemini không được code production sử dụng nên bị bỏ khỏi `.env.example`; AI Planner hiện gọi OpenAI từ Laravel server.
- `resources/js/app.ts`, `resources/js/ssr.ts`, `vite.config.ts` và `fene/` được giữ làm lịch sử/fallback tham chiếu nhưng không tham gia Next build hay Vercel upload. Wayfinder modules còn được UI import được version cùng frontend vì Vercel không có PHP để sinh chúng lúc build.

### Dependency decisions

| Nhóm | Quyết định | Packages/lý do |
|---|---|---|
| Runtime UI | KEEP | Vue, Inertia, Tailwind, Reka UI, VueUse, GSAP, Leaflet, Lucide giữ UI/UX và behavior hiện hữu |
| Realtime | KEEP + UPDATE | Laravel Echo/Pusher giữ protocol backend; lockfile nhận các bản vá transitive an toàn |
| Next platform | ADD | Next 16.3, React 19.2, React DOM và Next ESLint cho App Router/Vercel |
| Vue compatibility build | ADD | `vue-loader`, `ts-loader`, CSS/PostCSS loaders và Webpack để compile SFC trong Next |
| Vite toolchain | REPLACE | Vite, Laravel Vite plugin và Vue Vite plugin được thay bằng Next scripts/config; file cũ không còn trong build graph |
| Ziggy global helper assumption | REPLACE | Các URL lỗi dùng same-origin path/template literal; generated Wayfinder modules còn được UI dùng được version cùng frontend |
| Direct helpers | REMOVE | Không còn import trực tiếp axios hay lodash debounce; dùng native fetch và helper debounce typed nội bộ |

## 6. Bug/security fixes

- Bật đúng `MustVerifyEmail`; customer routes nay có customer-role middleware và booking request tự kiểm tra role.
- Fortify dùng password policy chung, chuẩn hóa email, từ chối tài khoản bị khóa ngay lúc authenticate.
- Public search chỉ trả dịch vụ hoạt động **và đã duyệt**.
- Sửa Admin Category theo schema thật (`parent_id`, `anh_dai_dien`, `thu_tu_hien_thi`), relation/count, uniqueness, cycle guard và bổ sung migration `icon`.
- Sửa tên bảng rollback sai trong migration cũ và xóa controller debug UTF-16 không dùng.
- Thay 10 lời gọi Ziggy `route()` không tồn tại bằng same-origin URL rõ ràng; thay axios trực tiếp bằng native fetch.
- Booking transitions, overlap check, wallet top-up/payment và withdrawal dùng database transaction + row lock để chống race/double processing.
- Realtime events được queue sau khi database commit, tránh gọi WebSocket trong lúc đang giữ row lock.
- Withdrawal liên kết trực tiếp với transaction qua `giao_dich_id`; vẫn có fallback cho dữ liệu legacy.
- Luồng mô phỏng nạp ví/VNPay bị khóa mặc định qua `PAYMENTS_DEMO_ENABLED=false`.
- Xóa cache config/route sinh tự động có thể chứa secret/absolute path; thêm ignore rules cho `.env.docker`, cache, SQLite, `.next` và Vercel state.
- Phát hiện local `.env` có các credential/key đã cấu hình nhưng không sao chép giá trị sang file mới. Do workspace không có metadata Git, nếu repository từng được chia sẻ cần rotate database, Reverb và AI keys liên quan.
- `npm audit fix` chỉ áp dụng các bản vá dependency tương thích và đưa audit về 0 vulnerability tại thời điểm kiểm tra.

## 7. Environment mapping

| Old | New | Scope | Secret | Vercel |
|---|---|---|---:|---:|
| `VITE_APP_NAME` | Metadata/`APP_NAME` | Server/build | Không | Không bắt buộc |
| `VITE_REVERB_ENABLED` | `NEXT_PUBLIC_REVERB_ENABLED` | Browser | Không | Có nếu realtime |
| `VITE_REVERB_APP_KEY` | `NEXT_PUBLIC_REVERB_APP_KEY` | Browser | Không | Có nếu realtime |
| `VITE_REVERB_HOST/PORT/SCHEME` | `NEXT_PUBLIC_REVERB_HOST/PORT/SCHEME` | Browser | Không | Có nếu realtime |
| — | `LARAVEL_API_URL` | Next server/proxy | Không public | Có |
| — | `NEXT_PUBLIC_SITE_URL` | Browser/metadata | Không | Có |
| `DATABASE_URL` (local file cũ) | `DB_URL` hoặc `DB_*` (`DATABASE_URL` vẫn được đọc tương thích) | Laravel server | Có | Không; backend host |
| `OPENAI_*` | Không đổi | Laravel server | Key là secret | Không; backend host |
| — | `PAYMENTS_DEMO_ENABLED` | Laravel server | Không | Không; backend host |

## 8. Validation và regression checklist

- `[PASS]` Registry tĩnh bao phủ đủ 53/53 Inertia pages; thiếu page sẽ fail rõ ràng thay vì fallback giả.
- `[PASS]` Public/customer/provider/admin/settings/auth URL được giữ nguyên qua catch-all + proxy.
- `[PASS]` Form, upload, CSRF/session, redirects và Inertia navigation tiếp tục dùng same-origin URLs.
- `[PASS]` Realtime channel/auth path giữ nguyên; Reverb được tách khỏi Vercel runtime.
- `[FIXED]` Auth/account lock/email verification/customer authorization.
- `[FIXED]` Admin categories, search moderation, undefined Ziggy route helper và undeclared debounce/axios usage.
- `[FIXED]` Booking/payment/withdrawal transaction safety và demo payment exposure.
- `[PASS]` TypeScript strict check và Next production build đã chạy thành công trong quá trình migration.
- `[PASS]` Production smoke test với Inertia backend stub: `/`, `/robots.txt`, `/sitemap.xml` và một POST mutation qua proxy đều trả 200; Vue bridge marker, robots rules và service sitemap đều hiện diện.
- `[BLOCKED]` PHP test suite chưa thể chạy trong máy hiện tại vì không có PHP executable và `vendor/` chưa được Composer cài; Docker daemon cũng không hoạt động.
- `[BLOCKED]` End-to-end authenticated/browser visual test cần backend PostgreSQL + Laravel + Reverb đang chạy và dữ liệu fixture hợp lệ.

## 9. Known limitations và recommendations

1. Đây là migration theo mô hình strangler: Next sở hữu routing/SSR metadata/deployment, nhưng UI feature layer vẫn là Vue/Inertia trong một client boundary. Muốn đạt React Server Component rendering cho toàn bộ body cần một phase chuyển 211 page/component/layout SFC sang React, làm theo từng domain với E2E visual parity.
2. Single-repository deploy không có nghĩa Laravel chạy trên Vercel. Backend, worker, scheduler và Reverb bắt buộc có hạ tầng riêng; đây là ràng buộc deployment chứ không phải lỗi build Next.
3. Upload local chỉ an toàn khi backend có persistent volume. Trước khi autoscale nhiều instance, chuyển sang S3/R2 và chạy migration URL/asset phù hợp.
4. Thanh toán ngoài chưa phải production gateway. Cần callback có chữ ký, idempotency key/unique constraint, ledger đối soát và webhook retry; giữ demo flag `false` cho tới khi hoàn tất.
5. `fene/` và Vite entry cũ không được build; có thể xóa trong một cleanup riêng sau khi đội dự án xác nhận không cần lịch sử prototype/fallback.
6. `favicon.png` hiện là ảnh 824×824 khoảng 916 KB; nên xuất lại favicon 32/48/192px tối ưu nhưng không đổi visual asset khi chưa có file thiết kế nguồn.
7. Khi có PHP runtime, chạy `composer install`, `php artisan migrate`, `php artisan test`, `php artisan route:list`, sau đó smoke test login/2FA/booking/payment-wallet/upload/chat/realtime trên staging trước production cutover.
