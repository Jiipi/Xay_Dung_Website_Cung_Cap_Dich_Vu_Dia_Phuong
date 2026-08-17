# Dalat Services

Nền tảng đặt dịch vụ địa phương tại Đà Lạt. Sử dụng stack **Laravel 12 + Inertia.js 2.0 + Vue 3.5 + Vite 7 + Tailwind CSS 4**.

## Kiến trúc

```text
Browser
  └─ Laravel 12 (PHP / Blade document shell + Inertia server adapter)
       ├─ Vue 3.5 + Inertia 2.0 (Client Single Page App qua Vite 7)
       ├─ PostgreSQL (Database)
       ├─ Queue worker + Scheduler
       ├─ Reverb WebSocket (Realtime chat & thấu kính thông báo)
       └─ Local/S3-compatible object storage
```

## Stack

- **Backend**: Laravel 12, Fortify (Auth/2FA), Reverb (WebSockets), PostgreSQL
- **Frontend**: Vue 3.5, Inertia.js 2.0, Tailwind CSS 4, Wayfinder
- **Build Tool**: Vite 7 (`@vitejs/plugin-vue`, `@tailwindcss/vite`)
- **Libraries**: GSAP, Leaflet, Laravel Echo/Pusher, Reka UI, VueUse, Lucide Icons
- **Package Manager**: npm (`package-lock.json`)

Yêu cầu: Node.js `>=20.9`, npm 10+, PHP 8.2+, Composer 2 và PostgreSQL.

## Cài đặt local

```bash
# 1. Cài đặt dependencies
npm install
composer install

# 2. Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# 3. Chạy migration & link storage
php artisan migrate
php artisan storage:link
```

Điền các thông tin DB (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) trong `.env`, sau đó chạy ứng dụng:

```bash
# Terminal 1: Laravel Backend
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2: Vite Frontend Dev Server
npm run dev

# Terminal 3 (tùy chọn): Realtime & Queue
php artisan reverb:start
php artisan queue:work
```

Mặc định ứng dụng chạy tại `http://127.0.0.1:8000`.

## Kiểm tra và Build

```bash
# Kiểm tra TypeScript typecheck
npm run types:check

# Kiểm tra Linter
npm run lint

# Build cho Production
npm run build

# Chạy PHP Test Suite
php artisan test
```

## Cấu trúc Dự án

- `resources/js/pages/`: 53 màn hình Vue/Inertia (Welcome, Services, Search, AI Planner, Customer, Provider, Admin, Auth, Settings, Chat...)
- `resources/js/components/`: Shared UI components (SiteHeader, SiteFooter, NotificationBell, Reka UI primitives...)
- `resources/js/layouts/`: Vue Layouts (CustomerLayout, ProviderLayout, AdminLayout, MarketplaceLayout, AuthLayout)
- `resources/views/app.blade.php`: Root HTML template
- `resources/css/app.css` & `tokens.css`: Styling Tailwind 4 & design tokens
- `routes/web.php` & `routes/settings.php`: Laravel Inertia routes
- `app/Http/Controllers/`: Laravel Controllers
- `app/Services/`: Business logic services (AI Planner, Booking, Service, Notification...)
- `app/Repositories/`: Eloquent Data Access Layer
