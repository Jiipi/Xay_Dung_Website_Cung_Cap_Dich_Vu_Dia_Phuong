<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Facebook, Mail, MapPin, Phone, Wrench } from 'lucide-vue-next';
</script>

<template>
    <footer class="site-footer">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2">
                        <div class="site-footer__logo">
                            <Wrench class="size-4" />
                        </div>
                        <span class="site-footer__brand">Dalat Services</span>
                    </div>
                    <p class="site-footer__desc">
                        Nền tảng đặt dịch vụ địa phương hàng đầu tại Đà Lạt. Kết nối khách hàng với thợ lành nghề, giá minh bạch.
                    </p>
                </div>

                <!-- Công ty -->
                <div>
                    <h4 class="site-footer__heading">Công ty</h4>
                    <nav class="site-footer__nav">
                        <Link href="/about">Giới thiệu</Link>
                        <Link href="/contact">Liên hệ</Link>
                        <Link href="/policy">Chính sách bảo mật</Link>
                    </nav>
                </div>

                <!-- Hỗ trợ -->
                <div>
                    <h4 class="site-footer__heading">Hỗ trợ</h4>
                    <nav class="site-footer__nav">
                        <Link href="/contact">Trung tâm trợ giúp</Link>
                        <Link href="/services">Khám phá dịch vụ</Link>
                        <Link href="/categories">Danh mục</Link>
                    </nav>
                </div>

                <!-- Liên hệ -->
                <div>
                    <h4 class="site-footer__heading">Liên hệ</h4>
                    <div class="site-footer__contact">
                        <div><Phone class="size-4" /> 0123 456 789</div>
                        <div><Mail class="size-4" /> support@dalatservices.vn</div>
                        <div><MapPin class="size-4" /> Đà Lạt, Lâm Đồng</div>
                    </div>
                </div>
            </div>

            <div class="site-footer__bottom">
                <p>&copy; 2026 Dalat Services. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="site-footer__social"><Facebook class="size-5" /></a>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.site-footer {
    margin-top: auto;
    border-top: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
}

.site-footer__logo {
    display: flex;
    width: 36px; height: 36px;
    align-items: center; justify-content: center;
    border-radius: var(--dl-radius-xl);
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    color: white;
}

.site-footer__brand {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dl-text);
}

.site-footer__desc {
    margin-top: var(--dl-space-3);
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--dl-text-muted);
}

.site-footer__heading {
    font-size: 0.8125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dl-text);
}

.site-footer__nav {
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-2);
    margin-top: var(--dl-space-4);
}
.site-footer__nav a {
    font-size: 0.875rem;
    color: var(--dl-text-muted);
    transition: var(--dl-transition-fast);
}
.site-footer__nav a:hover {
    color: var(--dl-brand);
}

.site-footer__contact {
    margin-top: var(--dl-space-4);
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-3);
}
.site-footer__contact div {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    font-size: 0.875rem;
    color: var(--dl-text-muted);
}

.site-footer__bottom {
    margin-top: var(--dl-space-10);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    gap: var(--dl-space-4);
    border-top: 1px solid var(--dl-warm-border);
    padding-top: var(--dl-space-6);
}
@media (min-width: 640px) {
    .site-footer__bottom { flex-direction: row; }
}
.site-footer__bottom p {
    font-size: 0.75rem;
    color: var(--dl-text-faint);
}

.site-footer__social {
    color: var(--dl-text-faint);
    transition: var(--dl-transition-fast);
}
.site-footer__social:hover {
    color: var(--dl-brand);
}
</style>
