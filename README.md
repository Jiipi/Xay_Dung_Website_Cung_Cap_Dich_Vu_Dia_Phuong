# Dalat Services

Nền tảng đặt dịch vụ địa phương tại Đà Lạt. Frontend chạy bằng Next.js App Router trên Vercel; Laravel tiếp tục giữ business logic, xác thực, PostgreSQL, upload, queue và realtime. Toàn bộ 53 màn hình Vue/Inertia hiện có được giữ nguyên qua một client boundary duy nhất để tránh regression UI trong lần migration này.

## Kiến trúc

```text
Browser
  └─ Next.js 16 App Router (Vercel, SSR metadata/routing)
       ├─ React Server Component lấy Inertia page payload
       ├─ Vue/Inertia compatibility boundary render UI hiện hữu
       └─ proxy.ts chuyển mutation/XHR/upload/realtime auth
            └─ Laravel 12 (PHP host bền vững)
                 ├─ PostgreSQL
                 ├─ queue worker + scheduler
                 ├─ Reverb WebSocket
                 └─ local/S3-compatible object storage
```

Next và Laravel được đặt sau cùng một public origin đối với trình duyệt. Vì vậy session cookie, CSRF và URL nội bộ hiện hữu không phải đổi sang CORS cross-origin.

## Stack

- Next.js 16.3 App Router, React 19, TypeScript strict
- Vue 3.5, Inertia 2, Tailwind CSS 4
- Laravel 12, Fortify, Reverb, PostgreSQL
- GSAP, Leaflet, Laravel Echo/Pusher
- npm (lockfile duy nhất: `package-lock.json`)

Yêu cầu: Node.js `>=20.9`, npm 10+, PHP 8.2+, Composer 2 và PostgreSQL.

## Cài đặt local

```bash
npm ci
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Điền biến môi trường local trong `.env`, sau đó chạy backend:

```bash
php artisan serve --host=127.0.0.1 --port=8000
php artisan queue:work
php artisan reverb:start
```

Chạy frontend ở terminal khác:

```bash
npm run dev
```

Mặc định frontend dùng `http://localhost:3000` và kết nối backend qua `LARAVEL_API_URL=http://127.0.0.1:8000`.

## Biến môi trường

| Biến | Nơi cấu hình | Public | Bắt buộc |
|---|---|---:|---:|
| `LARAVEL_API_URL` | Vercel | Không | Có |
| `NEXT_PUBLIC_SITE_URL` | Vercel | Có | Có |
| `NEXT_PUBLIC_REVERB_ENABLED` | Vercel | Có | Khi dùng realtime |
| `NEXT_PUBLIC_REVERB_APP_KEY` | Vercel | Có | Khi dùng realtime |
| `NEXT_PUBLIC_REVERB_HOST/PORT/SCHEME` | Vercel | Có | Khi dùng realtime |
| `APP_KEY` | Laravel host | Không | Có |
| `DB_URL` hoặc `DB_*` | Laravel host | Không | Có |
| `REVERB_APP_ID/KEY/SECRET` | Laravel host | `KEY` có thể public | Khi dùng realtime |
| `OPENAI_API_KEY` | Laravel host | Không | Khi bật AI Planner |
| `PAYMENTS_DEMO_ENABLED` | Laravel host | Không | Không; luôn `false` ở production |

Xem toàn bộ placeholder trong `.env.example`. Không đưa database credential, Reverb secret hay AI key vào biến `NEXT_PUBLIC_*`.

## Kiểm tra và build

```bash
npm run lint
npm run types:check
npm run build
npm audit
php artisan test
```

`php artisan test` cần PHP extensions và dependencies Composer. Các script frontend không yêu cầu Laravel hoạt động trong lúc build vì catch-all route được render động.

## Deploy frontend lên Vercel

1. Import repository, giữ Root Directory là root hiện tại và Framework Preset là Next.js.
2. Vercel tự dùng `npm ci` và `npm run build`; không cần custom server hay `vercel.json`.
3. Cấu hình các biến Vercel trong bảng trên cho Development, Preview và Production.
4. Đặt `NEXT_PUBLIC_SITE_URL` bằng domain tương ứng; đặt `LARAVEL_API_URL` bằng HTTPS origin riêng của Laravel.
5. Không deploy Laravel/Reverb/worker lên Vercel Functions. Dùng VPS, Laravel Cloud, Fly.io, Railway hoặc dịch vụ container/PHP bền vững.
6. Ở backend production dùng `SESSION_DOMAIN=null`, `SESSION_SECURE_COOKIE=true`, trusted proxy đúng và HTTPS. Reverb cần public WSS endpoint riêng.
7. Upload hiện được proxy từ `/storage/*`; với nhiều instance nên đổi `FILESYSTEM_DISK` sang S3/R2 thay cho local disk.

## Thanh toán

Luồng nạp ví và nút VNPay cũ chỉ là mô phỏng, không xác minh callback. Migration đã khóa chúng mặc định. Chỉ đặt `PAYMENTS_DEMO_ENABLED=true` ở local/demo; production cần adapter gateway có chữ ký, callback idempotent và đối soát trước khi bật thanh toán ngoài.

## Cấu trúc quan trọng

- `app/[[...path]]/page.tsx`: App Router catch-all, metadata và initial Inertia fetch
- `src/lib/inertia.ts`: server-only Laravel/Inertia client
- `src/components/vue-inertia-bridge.tsx`: React client boundary tối thiểu
- `resources/js/next-bridge.ts`: registry đủ 53 Vue pages
- `proxy.ts`: Next 16 request proxy cho mutation/XHR/storage/broadcast auth
- `resources/js/pages`, `components`, `layouts`: UI Vue hiện hữu
- `app/Http`, `app/Services`, `routes`: Laravel business layer
- `MIGRATION_REPORT.md`: audit, route inventory, bug fixes và giới hạn còn lại

Không chạy `php artisan config:cache` trước khi biến môi trường production đã đầy đủ. Các file `bootstrap/cache/*.php` là artifact sinh tự động và không được commit.
