<script setup lang="ts">
import { computed, ref } from 'vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    CalendarCheck,
    CalendarDays,
    ChevronDown,
    ClipboardList,
    Heart,
    LayoutDashboard,
    LogOut,
    Menu,
    Package,
    Search,
    Settings,
    Sparkles,
    Star,
    User,
    Wrench,
    X,
} from 'lucide-vue-next';
import { useAnimations } from '@/composables/useAnimations';

const { animateAutoHideHeader } = useAnimations();

const page = usePage();
const isMenuOpen = ref(false);
const isUserMenuOpen = ref(false);
const searchQuery = ref('');
const headerRef = ref<HTMLElement | null>(null);

// Auto-hide header on scroll down, show on scroll up
animateAutoHideHeader(headerRef, { hideDistance: 120 });

const isLoggedIn = computed(() => !!page.props.auth?.user);
const userName = computed(() => page.props.auth?.user?.name ?? '');
const userEmail = computed(() => page.props.auth?.user?.email ?? '');
const userRole = computed(() => (page.props.auth as any)?.role ?? '');
const userInitial = computed(() => userName.value?.charAt(0)?.toUpperCase() ?? 'U');
const isProvider = computed(() => userRole.value === 'Nhà cung cấp');
const isAdmin = computed(() => userRole.value === 'Admin');

const dashboardUrl = computed(() => {
    if (userRole.value === 'Admin') return '/admin/dashboard';
    if (userRole.value === 'Nhà cung cấp') return '/provider/dashboard';
    return '/customer/dashboard';
});

const roleLabel = computed(() => {
    if (userRole.value === 'Admin') return 'Quản trị viên';
    if (userRole.value === 'Nhà cung cấp') return 'Nhà cung cấp';
    return 'Khách hàng';
});

function handleSearch() {
    if (searchQuery.value.trim()) {
        router.get('/search', { q: searchQuery.value });
    }
}

function closeUserMenu() {
    isUserMenuOpen.value = false;
}

function closeUserMenuSoon() {
    setTimeout(() => {
        closeUserMenu();
    }, 150);
}
</script>

<template>
    <header ref="headerRef" class="site-header">
        <div class="site-header__inner">
            <!-- Logo -->
            <Link :href="isAdmin ? '/admin/dashboard' : isProvider ? '/provider/dashboard' : '/'" class="site-header__logo">
                <div class="site-header__logo-icon">
                    <Wrench class="size-5" />
                </div>
                <div class="hidden sm:block">
                    <p class="site-header__logo-label">Local Service</p>
                    <h1 class="site-header__logo-title">Dalat Services</h1>
                </div>
            </Link>

            <!-- Search Bar Desktop -->
            <div v-if="!isProvider" class="hidden max-w-xl flex-1 md:flex">
                <form @submit.prevent="handleSearch" class="site-header__search">
                    <Search class="site-header__search-icon" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Tìm dịch vụ: sửa điều hòa, tour săn mây..."
                        class="site-header__search-input"
                    />
                    <button type="submit" class="site-header__search-btn">Tìm</button>
                </form>
            </div>

            <!-- Nav + Auth Desktop -->
            <div class="hidden items-center gap-6 md:flex">
                <!-- Provider Nav -->
                <nav v-if="isProvider" class="flex items-center gap-5 text-sm font-medium" style="color: var(--dl-text-muted);">
                    <Link href="/provider/dashboard" class="site-header__nav-link">
                        <LayoutDashboard class="size-3.5" /> Tổng quan
                    </Link>
                    <Link href="/provider/services" class="site-header__nav-link">
                        <Package class="size-3.5" /> Dịch vụ
                    </Link>
                    <Link href="/provider/bookings" class="site-header__nav-link">
                        <ClipboardList class="size-3.5" /> Đơn hàng
                    </Link>
                    <Link href="/provider/reviews" class="site-header__nav-link">
                        <Star class="size-3.5" /> Đánh giá
                    </Link>
                    <Link href="/provider/availability" class="site-header__nav-link">
                        <CalendarCheck class="size-3.5" /> Lịch rảnh
                    </Link>
                    <Link href="/provider/profile" class="site-header__nav-link">
                        <User class="size-3.5" /> Hồ sơ
                    </Link>
                </nav>

                <!-- Public/Customer Nav -->
                <nav v-else class="flex items-center gap-5 text-sm font-medium" style="color: var(--dl-text-muted);">
                    <Link href="/services" class="site-header__nav-link">Khám phá</Link>
                    <Link href="/ai-planner" class="site-header__nav-link">
                        <span class="site-header__ai-badge">AI</span>
                        Lên lịch trình
                    </Link>
                    <Link href="/categories" class="site-header__nav-link">Danh mục</Link>
                </nav>

                <div class="flex items-center gap-2">
                    <template v-if="isLoggedIn">
                        <NotificationBell />
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button
                                class="site-header__user-btn"
                                @click="isUserMenuOpen = !isUserMenuOpen"
                                @blur="closeUserMenuSoon"
                            >
                                <span class="site-header__avatar">{{ userInitial }}</span>
                                <span class="hidden max-w-[120px] truncate lg:inline" style="color: var(--dl-text);">{{ userName }}</span>
                                <ChevronDown class="size-3.5" style="color: var(--dl-text-faint);" />
                            </button>

                            <!-- Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="scale-95 opacity-0"
                                enter-to-class="scale-100 opacity-100"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="scale-100 opacity-100"
                                leave-to-class="scale-95 opacity-0"
                            >
                                <div v-show="isUserMenuOpen" class="site-header__dropdown">
                                    <!-- User info -->
                                    <div class="site-header__dropdown-header">
                                        <p class="site-header__dropdown-name">{{ userName }}</p>
                                        <p class="site-header__dropdown-email">{{ userEmail }}</p>
                                        <span class="site-header__dropdown-role">{{ roleLabel }}</span>
                                    </div>
                                    <!-- Links -->
                                    <div class="p-2">
                                        <Link :href="dashboardUrl" class="site-header__dropdown-link">
                                            <LayoutDashboard class="size-4" style="color: var(--dl-text-faint);" /> Dashboard
                                        </Link>
                                        <Link v-if="userRole !== 'Nhà cung cấp'" href="/customer/bookings" class="site-header__dropdown-link">
                                            <CalendarDays class="size-4" style="color: var(--dl-text-faint);" /> Booking của tôi
                                        </Link>
                                        <Link v-if="userRole !== 'Nhà cung cấp'" href="/customer/favorites" class="site-header__dropdown-link">
                                            <Heart class="size-4" style="color: var(--dl-text-faint);" /> Yêu thích
                                        </Link>
                                        <Link href="/settings/profile" class="site-header__dropdown-link">
                                            <Settings class="size-4" style="color: var(--dl-text-faint);" /> Cài đặt
                                        </Link>
                                    </div>
                                    <!-- Logout -->
                                    <div class="site-header__dropdown-footer">
                                        <Link href="/logout" method="post" as="button" class="site-header__dropdown-link site-header__dropdown-link--danger">
                                            <LogOut class="size-4" /> Đăng xuất
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </template>

                    <template v-else>
                        <Link href="/login" class="site-header__login-btn">Đăng nhập</Link>
                        <Link href="/register" class="site-header__register-btn">Đăng ký</Link>
                    </template>
                </div>
            </div>

            <!-- Mobile hamburger -->
            <button class="site-header__hamburger" @click="isMenuOpen = !isMenuOpen">
                <Menu v-if="!isMenuOpen" class="size-6" />
                <X v-else class="size-6" />
            </button>
        </div>

        <!-- Mobile Search -->
        <div v-if="!isProvider" class="site-header__mobile-search">
            <form @submit.prevent="handleSearch" class="site-header__search site-header__search--mobile">
                <Search class="site-header__search-icon" />
                <input v-model="searchQuery" type="text" placeholder="Tìm dịch vụ..." class="site-header__search-input" />
                <button type="submit" class="site-header__search-btn">Tìm</button>
            </form>
        </div>

        <!-- Mobile Menu -->
        <div v-show="isMenuOpen" class="site-header__mobile-menu">
            <!-- Provider Mobile Nav -->
            <nav v-if="isProvider" class="site-header__mobile-nav">
                <Link href="/provider/dashboard" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <LayoutDashboard class="size-5" /> Tổng quan
                </Link>
                <Link href="/provider/services" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <Package class="size-5" /> Dịch vụ của tôi
                </Link>
                <Link href="/provider/bookings" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <ClipboardList class="size-5" /> Đơn hàng
                </Link>
                <Link href="/provider/reviews" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <Star class="size-5" /> Đánh giá
                </Link>
                <Link href="/provider/availability" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <CalendarCheck class="size-5" /> Lịch rảnh
                </Link>
                <Link href="/provider/profile" class="site-header__mobile-link" @click="isMenuOpen = false">
                    <User class="size-5" /> Hồ sơ nhà cung cấp
                </Link>
            </nav>

            <!-- Public/Customer Mobile Nav -->
            <nav v-else class="site-header__mobile-nav">
                <Link href="/services" @click="isMenuOpen = false">Khám phá</Link>
                <Link href="/ai-planner" class="flex items-center gap-2" @click="isMenuOpen = false">
                    <Sparkles class="size-4" style="color: var(--dl-accent);" /> AI Lên lịch trình
                </Link>
                <Link href="/categories" @click="isMenuOpen = false">Danh mục</Link>
            </nav>

            <div class="mt-6 flex flex-col gap-2">
                <template v-if="isLoggedIn">
                    <div class="site-header__mobile-user">
                        <span class="site-header__avatar">{{ userInitial }}</span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--dl-text);">{{ userName }}</p>
                            <p class="text-xs" style="color: var(--dl-text-muted);">{{ roleLabel }}</p>
                        </div>
                    </div>
                    <Link :href="dashboardUrl" class="site-header__mobile-cta" @click="isMenuOpen = false">Dashboard</Link>
                    <Link href="/logout" method="post" as="button" class="site-header__mobile-logout" @click="isMenuOpen = false">Đăng xuất</Link>
                </template>
                <template v-else>
                    <Link href="/login" class="site-header__login-btn site-header__login-btn--full" @click="isMenuOpen = false">Đăng nhập</Link>
                    <Link href="/register" class="site-header__register-btn site-header__register-btn--full" @click="isMenuOpen = false">Đăng ký</Link>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.site-header {
    position: sticky;
    top: 0;
    z-index: 50;
    border-bottom: 1px solid var(--dl-warm-border);
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
}

.site-header__inner {
    max-width: 1280px;
    margin-inline: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--dl-space-4);
    padding: var(--dl-space-3) var(--dl-space-4);
}
@media (min-width: 640px) { .site-header__inner { padding-inline: var(--dl-space-6); } }

/* Logo */
.site-header__logo {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    flex-shrink: 0;
}
.site-header__logo-icon {
    display: flex;
    width: 40px; height: 40px;
    align-items: center; justify-content: center;
    border-radius: var(--dl-radius-xl);
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    color: white;
    box-shadow: 0 4px 12px rgba(45, 106, 79, 0.25);
}
.site-header__logo-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: var(--dl-text-faint);
}
.site-header__logo-title {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--dl-text);
}

/* Search */
.site-header__search {
    display: flex;
    width: 100%;
    align-items: center;
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-bg);
    padding: 6px 6px 6px var(--dl-space-4);
    transition: var(--dl-transition-fast);
}
.site-header__search:focus-within {
    border-color: var(--dl-brand-light);
    background: white;
    box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.08);
}
.site-header__search-icon {
    width: 16px; height: 16px;
    margin-right: var(--dl-space-2);
    color: var(--dl-text-faint);
    flex-shrink: 0;
}
.site-header__search-input {
    flex: 1;
    background: transparent;
    font-size: 14px;
    color: var(--dl-text);
    outline: none;
    min-width: 0;
}
.site-header__search-input::placeholder { color: var(--dl-text-faint); }
.site-header__search-btn {
    border-radius: var(--dl-radius-full);
    background: var(--dl-brand);
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    transition: var(--dl-transition-fast);
}
.site-header__search-btn:hover { background: var(--dl-brand-hover); }
.site-header__search-btn:active { transform: scale(0.95); }

/* Nav Links */
.site-header__nav-link {
    display: flex;
    align-items: center;
    gap: 6px;
    transition: var(--dl-transition-fast);
}
.site-header__nav-link:hover { color: var(--dl-text); }

.site-header__ai-badge {
    border-radius: var(--dl-radius-md);
    background: linear-gradient(135deg, var(--dl-accent), #d4502e);
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 700;
    color: white;
}

/* User Button */
.site-header__user-btn {
    display: flex;
    align-items: center;
    gap: var(--dl-space-2);
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    padding: 6px 12px 6px 6px;
    font-size: 14px;
    font-weight: 500;
    transition: var(--dl-transition-fast);
}
.site-header__user-btn:hover {
    border-color: var(--dl-brand-light);
    background: var(--dl-brand-surface);
}

.site-header__avatar {
    display: flex;
    width: 28px; height: 28px;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--dl-brand), var(--dl-brand-light));
    font-size: 11px;
    font-weight: 700;
    color: white;
}

/* Dropdown */
.site-header__dropdown {
    position: absolute;
    right: 0; top: 100%;
    margin-top: 8px;
    width: 16rem;
    border-radius: var(--dl-radius-2xl);
    border: 1px solid var(--dl-warm-border);
    background: var(--dl-warm-surface);
    box-shadow: var(--dl-shadow-xl);
    overflow: hidden;
    transform-origin: top right;
    z-index: 60;
}
.site-header__dropdown-header {
    border-bottom: 1px solid var(--dl-warm-border);
    padding: var(--dl-space-3) var(--dl-space-4);
}
.site-header__dropdown-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--dl-text);
}
.site-header__dropdown-email {
    font-size: 12px;
    color: var(--dl-text-muted);
}
.site-header__dropdown-role {
    display: inline-flex;
    margin-top: 6px;
    border-radius: var(--dl-radius-full);
    background: var(--dl-brand-surface);
    padding: 2px 10px;
    font-size: 10px;
    font-weight: 700;
    color: var(--dl-brand);
}
.site-header__dropdown-link {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    border-radius: var(--dl-radius-xl);
    padding: var(--dl-space-2) var(--dl-space-3);
    font-size: 14px;
    color: var(--dl-text-muted);
    transition: var(--dl-transition-fast);
    width: 100%;
}
.site-header__dropdown-link:hover {
    background: var(--dl-brand-surface);
    color: var(--dl-brand);
}
.site-header__dropdown-link--danger {
    color: #dc2626;
}
.site-header__dropdown-link--danger:hover {
    background: #fef2f2;
    color: #b91c1c;
}
.site-header__dropdown-footer {
    border-top: 1px solid var(--dl-warm-border);
    padding: var(--dl-space-2);
}

/* Auth buttons */
.site-header__login-btn {
    border-radius: var(--dl-radius-full);
    border: 1px solid var(--dl-warm-border);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    color: var(--dl-text);
    transition: var(--dl-transition-fast);
}
.site-header__login-btn:hover { background: var(--dl-warm-bg); }
.site-header__login-btn--full { text-align: center; padding: 12px; }

.site-header__register-btn {
    border-radius: var(--dl-radius-full);
    background: var(--dl-brand);
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    color: white;
    transition: var(--dl-transition-fast);
}
.site-header__register-btn:hover { background: var(--dl-brand-hover); }
.site-header__register-btn--full { text-align: center; padding: 12px; }

/* Mobile */
.site-header__hamburger {
    padding: var(--dl-space-2);
    color: var(--dl-text-muted);
    display: block;
}
@media (min-width: 768px) { .site-header__hamburger { display: none; } }

.site-header__mobile-search {
    border-top: 1px solid var(--dl-warm-border);
    padding: var(--dl-space-2) var(--dl-space-4);
    display: block;
}
@media (min-width: 768px) { .site-header__mobile-search { display: none; } }
.site-header__search--mobile {
    padding: 4px 4px 4px 12px;
}

.site-header__mobile-menu {
    border-top: 1px solid var(--dl-warm-border);
    background: white;
    padding: var(--dl-space-6) var(--dl-space-4);
    box-shadow: var(--dl-shadow-lg);
    display: block;
}
@media (min-width: 768px) { .site-header__mobile-menu { display: none; } }

.site-header__mobile-nav {
    display: flex;
    flex-direction: column;
    gap: var(--dl-space-4);
    font-size: 1rem;
    font-weight: 500;
    color: var(--dl-text-muted);
}
.site-header__mobile-link {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
}
.site-header__mobile-user {
    display: flex;
    align-items: center;
    gap: var(--dl-space-3);
    border-radius: var(--dl-radius-xl);
    background: var(--dl-brand-surface);
    padding: var(--dl-space-3) var(--dl-space-4);
    margin-bottom: var(--dl-space-2);
}
.site-header__mobile-cta {
    border-radius: var(--dl-radius-full);
    background: var(--dl-brand);
    padding: 12px 20px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    color: white;
}
.site-header__mobile-logout {
    width: 100%;
    border-radius: var(--dl-radius-full);
    border: 1px solid #fecaca;
    background: #fef2f2;
    padding: 12px 20px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    color: #dc2626;
}
</style>
