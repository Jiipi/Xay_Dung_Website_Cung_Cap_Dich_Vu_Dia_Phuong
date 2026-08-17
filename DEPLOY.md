# Hướng dẫn Deploy Dalat Services

> **Stack**: Laravel 12 + Inertia.js 2.0 + Vue 3.5 + Vite 7 + Tailwind CSS 4  
> **Backend**: Render (Web Service + PostgreSQL + Background Worker)  
> **Frontend**: Vite build assets nằm trong cùng backend (Inertia monolith) — KHÔNG tách riêng  
> **Tùy chọn**: Vercel chỉ dùng làm CDN reverse proxy nếu muốn domain riêng + edge caching

---

## ⚠️ Lưu ý quan trọng

Dự án này dùng **Inertia.js** — frontend và backend là **một khối** (monolith). Laravel render trang HTML qua `app.blade.php`, Vite build JS/CSS vào `public/build/`. **Không thể tách riêng frontend ra Vercel** như Next.js được.

**Kiến trúc deploy đúng:**

```
Browser ──► Render Web Service (Nginx + PHP-FPM)
               ├── Laravel 12 (serve HTML + API)
               ├── public/build/ (Vite assets: JS, CSS, images)
               └── PostgreSQL (Render managed DB)
```

---

## 📦 Phần 1: Deploy Backend + Frontend lên Render

### 1.1. Chuẩn bị Dockerfile cho Production

Dockerfile hiện tại dùng `php-fpm` cần kết hợp với Nginx. Trên Render, cách tốt nhất là dùng **một container** chạy cả Nginx + PHP-FPM.

Tạo file `Dockerfile.render` ở root dự án:

```dockerfile
# ============================================================
# Stage 1: Build frontend assets (Node.js)
# ============================================================
FROM node:22-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY . .
RUN npm run build

# ============================================================
# Stage 2: Install PHP dependencies (Composer)
# ============================================================
FROM composer:latest AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize

# ============================================================
# Stage 3: Production image (Nginx + PHP-FPM)
# ============================================================
FROM php:8.4-fpm-alpine

# Install system deps + Nginx
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    bash \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd opcache

# OPcache config
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Nginx config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Supervisor config (runs both Nginx + PHP-FPM)
RUN mkdir -p /etc/supervisor.d
COPY <<'EOF' /etc/supervisor.d/app.ini
[supervisord]
nodaemon=true
logfile=/dev/stdout
logfile_maxbytes=0

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

WORKDIR /var/www

# Copy app source
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend /app/public/build public/build

# Copy PHP vendor from Stage 2
COPY --from=composer /app/vendor vendor

# Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
```

### 1.2. Cập nhật Nginx config cho Render

Sửa `docker/nginx/default.conf` — thay `app:9000` bằng `127.0.0.1:9000` (vì Nginx và PHP-FPM chạy cùng container):

```nginx
server {
    listen 80;
    index index.php index.html;
    server_name _;
    root /var/www/public;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 256;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;

    # Vite build assets — cache 1 year (hashed filenames)
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
        try_files $uri =404;
    }

    # Static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;

        fastcgi_buffering on;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 16 16k;
        fastcgi_connect_timeout 60s;
        fastcgi_read_timeout 60s;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 1.3. Tạo Render Web Service

1. Đăng nhập [Render Dashboard](https://dashboard.render.com)
2. **New** → **Web Service**
3. Kết nối GitHub repo
4. Cấu hình:

| Thiết lập | Giá trị |
|---|---|
| **Name** | `dalat-services` |
| **Region** | Singapore (`sin`) |
| **Runtime** | Docker |
| **Dockerfile Path** | `Dockerfile.render` |
| **Instance Type** | Starter ($7/tháng) hoặc Standard ($25/tháng) |

5. Nhấn **Create Web Service**

### 1.4. Tạo PostgreSQL trên Render

1. **New** → **PostgreSQL**
2. Cấu hình:

| Thiết lập | Giá trị |
|---|---|
| **Name** | `dalat-services-db` |
| **Database** | `dalat_services` |
| **User** | `dalat_services` |
| **Region** | Singapore (cùng region với Web Service) |
| **Plan** | Free hoặc Starter |

3. Sau khi tạo xong, copy **Internal Database URL** (dạng `postgres://dalat_services:xxx@dpg-xxx/dalat_services`)

### 1.5. Tạo Background Worker (Queue)

1. **New** → **Background Worker**
2. Kết nối cùng repo
3. Cấu hình:

| Thiết lập | Giá trị |
|---|---|
| **Name** | `dalat-services-worker` |
| **Runtime** | Docker |
| **Dockerfile Path** | `Dockerfile.render` |
| **Docker Command** | `php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600` |

4. Set cùng Environment Variables như Web Service

---

## 🔐 Phần 2: Biến Môi Trường trên Render

Vào **Web Service** → **Environment** → thêm các biến:

### Biến bắt buộc

```env
# ─── App ───────────────────────────────────────
APP_NAME="Dalat Services"
APP_ENV=production
APP_KEY=base64:xxxxx                    # Chạy: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://dalat-services.onrender.com
APP_LOCALE=vi
APP_FALLBACK_LOCALE=vi
APP_FAKER_LOCALE=vi_VN

# ─── Database ──────────────────────────────────
DB_CONNECTION=pgsql
DB_URL=postgres://dalat_services:PASSWORD@dpg-xxx-a.singapore-postgres.render.com/dalat_services
# Hoặc tách ra:
# DB_HOST=dpg-xxx-a.singapore-postgres.render.com
# DB_PORT=5432
# DB_DATABASE=dalat_services
# DB_USERNAME=dalat_services
# DB_PASSWORD=your-password-here

# ─── Session & Cache ──────────────────────────
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database

# ─── Security ─────────────────────────────────
BCRYPT_ROUNDS=12
SESSION_ENCRYPT=true
SESSION_SAME_SITE=lax

# ─── Mail ─────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com               # hoặc mailgun, ses, etc.
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@dalatservices.com
MAIL_FROM_NAME="${APP_NAME}"

# ─── Storage ──────────────────────────────────
FILESYSTEM_DISK=local                   # Đổi sang s3 nếu dùng cloud storage
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=ap-southeast-1
# AWS_BUCKET=dalat-services

# ─── OpenAI (AI Planner) ─────────────────────
OPENAI_API_KEY=sk-xxx
OPENAI_MODEL=gpt-4o
OPENAI_TIMEOUT=20

# ─── Payments ─────────────────────────────────
PAYMENTS_DEMO_ENABLED=false
```

### Biến Vite (Frontend) — bắt buộc prefix `VITE_`

```env
VITE_APP_NAME="Dalat Services"
VITE_REVERB_ENABLED=false
VITE_REVERB_APP_KEY=your-reverb-key
VITE_REVERB_HOST=dalat-services.onrender.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

> **Lưu ý**: Biến `VITE_*` được Vite inject vào JS **lúc build time**, không phải runtime. Nếu thay đổi, phải **rebuild** (re-deploy).

### Biến Reverb (tùy chọn — nếu dùng WebSocket)

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=dalat-prod
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https
```

---

## 🔧 Phần 3: Chạy Migration lần đầu

Sau khi deploy thành công, vào **Web Service** → **Shell** tab:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Hoặc thêm vào `entrypoint.sh` để tự động chạy:

```bash
if [ "${APP_ENV}" = "production" ]; then
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi
```

---

## 🌐 Phần 4: Tùy chọn — Vercel làm CDN/Reverse Proxy

Nếu muốn dùng **domain riêng trên Vercel** + hưởng edge caching cho assets, bạn có thể setup Vercel làm reverse proxy trỏ về Render:

### 4.1. Tạo `vercel.json` ở root

```json
{
  "rewrites": [
    {
      "source": "/(.*)",
      "destination": "https://dalat-services.onrender.com/$1"
    }
  ],
  "headers": [
    {
      "source": "/build/(.*)",
      "headers": [
        { "key": "Cache-Control", "value": "public, max-age=31536000, immutable" }
      ]
    }
  ]
}
```

### 4.2. Deploy lên Vercel

```bash
npm i -g vercel
vercel --prod
```

### 4.3. Cấu hình trên Vercel

1. Vào [Vercel Dashboard](https://vercel.com/dashboard)
2. Import project → chọn repo
3. **Framework Preset**: `Other`
4. **Build Command**: để trống (không build gì trên Vercel)
5. **Output Directory**: để trống
6. **Environment Variables**: không cần (mọi thứ chạy trên Render)

### 4.4. Custom Domain

1. Vercel Dashboard → **Settings** → **Domains**
2. Thêm domain: `dalatservices.com`
3. Cập nhật DNS tại registrar (Namecheap, Cloudflare...):
   - **CNAME**: `cname.vercel-dns.com`
4. Sau khi domain active, cập nhật biến trên **Render**:
   ```
   APP_URL=https://dalatservices.com
   ```

---

## 📋 Phần 5: Checklist Deploy

### Trước khi deploy:

- [ ] `npm run build` chạy thành công ở local
- [ ] `php artisan test` pass (nếu có test)
- [ ] Đã tạo `APP_KEY` bằng `php artisan key:generate --show`
- [ ] Đã tạo PostgreSQL trên Render và copy connection string
- [ ] Đã chuẩn bị SMTP credentials cho mail

### Sau khi deploy:

- [ ] Truy cập `https://dalat-services.onrender.com` — trang chủ load OK
- [ ] Đăng ký / Đăng nhập hoạt động
- [ ] Tạo booking thử — kiểm tra database write
- [ ] Kiểm tra `public/build/` assets load đúng (DevTools → Network → không có 404)
- [ ] Kiểm tra mail gửi được (reset password, notifications)
- [ ] Queue worker xử lý job (kiểm tra bảng `jobs` trống sau khi trigger)

---

## 🔄 Phần 6: CI/CD Tự động

Render tự động deploy khi push code lên branch `main`. Flow:

```
git push origin main
    ↓
Render detect push
    ↓
Docker build (Stage 1: npm run build → Stage 2: composer install → Stage 3: image)
    ↓
Deploy container mới
    ↓
Health check pass → live
```

### Tắt auto-deploy (nếu muốn):

Render Dashboard → Web Service → **Settings** → **Auto-Deploy** → Off

---

## 💰 Chi phí ước tính (Render)

| Service | Plan | Giá/tháng |
|---|---|---|
| Web Service | Starter | $7 |
| PostgreSQL | Free (90 ngày) / Starter | $0 – $7 |
| Background Worker | Starter | $7 |
| **Tổng** | | **$7 – $21/tháng** |

> **Free tier**: Render cho dùng Web Service miễn phí nhưng sẽ spin down sau 15 phút không hoạt động (cold start ~30s). PostgreSQL free giới hạn 90 ngày.

---

## 🛠️ Troubleshooting

### Lỗi "Mixed Content" (HTTP/HTTPS)

Thêm vào `.env`:
```env
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
```

Và trong `app/Http/Middleware/TrustProxies.php`:
```php
protected $proxies = '*';
```

### Lỗi "419 CSRF Token Mismatch"

Kiểm tra:
1. `SESSION_DRIVER=database` và bảng `sessions` đã migrate
2. `SESSION_DOMAIN` để `null` (hoặc set đúng domain)
3. `SESSION_SECURE_COOKIE=true` nếu dùng HTTPS

### Assets không load (404 trên `/build/*`)

1. Kiểm tra Stage 1 Docker build log — `npm run build` phải thành công
2. Kiểm tra `public/build/manifest.json` tồn tại trong container:
   ```bash
   # Render Shell
   ls -la /var/www/public/build/
   ```

### Queue không chạy

Kiểm tra Background Worker log trên Render Dashboard. Đảm bảo `DB_URL` giống Web Service.
