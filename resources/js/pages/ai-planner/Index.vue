<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BadgeCheck,
    Bed,
    Bot,
    Calendar,
    Car,
    ChevronDown,
    ChevronRight,
    ChevronUp,
    Coffee,
    Home,
    Loader2,
    MapPin,
    MessageCircle,
    Send,
    Sparkles,
    Star,
    Utensils,
    Users,
    Wallet,
    X,
} from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';
import { toast } from 'vue-sonner';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';

type ServiceCard = {
    id: number;
    title: string;
    provider: string;
    rating: number;
    reviews: number;
    price: number;
    image: string;
    category?: string;
    category_name?: string;
    location?: string;
};

type ActivityItem = {
    time: string;
    name: string;
    desc: string;
    cost: number;
    icon: string;
    serviceId: number | null;
};

type DayPlan = {
    day: number;
    title: string;
    activities: ActivityItem[];
};

type PlanResult = {
    title: string;
    subtitle: string;
    summary: string;
    totalBudget: number;
    budgetBreakdown: Record<string, number>;
    days: DayPlan[];
    recommendedServices: ServiceCard[];
    insights: string[];
};

type ChatMessage = {
    role: 'ai' | 'user';
    text: string;
};

const props = withDefaults(
    defineProps<{
        recommendedServices?: ServiceCard[];
        availableLocations?: string[];
    }>(),
    {
        recommendedServices: () => [],
        availableLocations: () => [],
    },
);

const page = usePage();
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const storageKey = computed(() => {
    const userId = (page.props as any).auth?.user?.id ?? 'guest';
    return `ai_planner_saved_${userId}`;
});
const isAuthenticated = computed(() => Boolean((page.props as any).auth?.user));

const prompt = ref('');
const location = ref('');
const duration = ref('');
const budget = ref('');
const numPeople = ref(2);
const showAdvanced = ref(false);
const preferences = ref<string[]>([]);
const loading = ref(false);
const plannerError = ref('');
const plannerSource = ref<string | null>(null);
const activeTab = ref<'itinerary' | 'services' | 'budget'>('itinerary');
const result = ref<PlanResult | null>(null);
const savedAt = ref<string | null>(null);

const chatOpen = ref(false);
const chatInput = ref('');
const chatLoading = ref(false);
const chatMessages = ref<ChatMessage[]>([
    { role: 'ai', text: 'Xin chào! Mình có thể giúp bạn lên lịch trình hoặc tìm dịch vụ phù hợp.' },
]);

const locations = computed(() => {
    const major = ['Đà Lạt', 'Hà Nội', 'Đà Nẵng', 'Hồ Chí Minh', 'Phú Quốc', 'Vũng Tàu'];
    return [...new Set([...major, ...(props.availableLocations || [])])];
});

const durations = ['1N', '2N1Đ', '3N2Đ', '5N4Đ', '7N6Đ'];
const prefOptions = [
    { value: 'nature', label: '🌿 Thiên nhiên' },
    { value: 'food', label: '🍜 Ẩm thực' },
    { value: 'adventure', label: '🧗 Phiêu lưu' },
    { value: 'culture', label: '🏛️ Văn hóa' },
    { value: 'relax', label: '🧘 Thư giãn' },
    { value: 'photo', label: '📸 Check-in' },
];
const suggestedPrompts = [
    'Cần sửa điều hòa gấp tại Đà Lạt',
    'Lịch trình 2N1Đ Đà Nẵng cho cặp đôi',
    'Tour săn mây giá tốt cho 4 người',
    'Thuê xe máy Đà Lạt cuối tuần',
];

function ensureAuthenticated(actionLabel: string): boolean {
    if (isAuthenticated.value) return true;

    toast.error(`Vui lòng đăng nhập để ${actionLabel}.`);
    window.setTimeout(() => {
        window.location.href = '/login';
    }, 700);

    return false;
}

function usePrompt(text: string) {
    prompt.value = text;
}

function togglePref(pref: string) {
    const idx = preferences.value.indexOf(pref);
    if (idx === -1) preferences.value.push(pref);
    else preferences.value.splice(idx, 1);
}

function budgetAmount(): number {
    if (!budget.value) return 0;
    return Number.parseInt(String(budget.value).replace(/\D/g, ''), 10) || 0;
}

function setBudgetAmount(amount: number) {
    budget.value = new Intl.NumberFormat('vi-VN').format(Math.max(0, amount));
}

function plannerPayload() {
    return {
        prompt: prompt.value.trim(),
        location: location.value,
        duration: duration.value,
        budget_amount: budgetAmount(),
        budget_label: budget.value,
        num_people: numPeople.value,
        preferences: preferences.value,
    };
}

function applyResolvedInput(resolvedInput: any) {
    if (!resolvedInput || typeof resolvedInput !== 'object') return;

    if (typeof resolvedInput.prompt === 'string') {
        prompt.value = resolvedInput.prompt;
    }

    if (typeof resolvedInput.location === 'string' && resolvedInput.location.trim()) {
        location.value = resolvedInput.location.trim();
    }

    if (typeof resolvedInput.duration === 'string' && resolvedInput.duration.trim()) {
        duration.value = resolvedInput.duration.trim();
    }

    if (typeof resolvedInput.budgetAmount === 'number' && Number.isFinite(resolvedInput.budgetAmount)) {
        setBudgetAmount(resolvedInput.budgetAmount);
    }

    if (typeof resolvedInput.numPeople === 'number' && Number.isFinite(resolvedInput.numPeople)) {
        numPeople.value = Math.max(1, Math.min(20, resolvedInput.numPeople));
    }

    if (Array.isArray(resolvedInput.preferences)) {
        preferences.value = resolvedInput.preferences.filter((value: unknown) => typeof value === 'string');
    }
}

function hydratePlanResult(raw: any): PlanResult {
    return {
        title: raw?.title ?? `Lịch trình ${duration.value} tại ${location.value}`,
        subtitle: raw?.subtitle ?? `Dành cho ${numPeople.value} người`,
        summary: raw?.summary ?? 'Kế hoạch được tạo từ dữ liệu thực tế và AI.',
        totalBudget: Number(raw?.totalBudget ?? budgetAmount()),
        budgetBreakdown: {
            accommodation: Number(raw?.budgetBreakdown?.accommodation ?? 0),
            transport: Number(raw?.budgetBreakdown?.transport ?? 0),
            sightseeing: Number(raw?.budgetBreakdown?.sightseeing ?? 0),
            food: Number(raw?.budgetBreakdown?.food ?? 0),
            misc: Number(raw?.budgetBreakdown?.misc ?? 0),
        },
        days: Array.isArray(raw?.days)
            ? raw.days.map((day: any) => ({
                day: Number(day?.day ?? 1),
                title: day?.title ?? 'Ngày khám phá',
                activities: Array.isArray(day?.activities)
                    ? day.activities.map((act: any) => ({
                        time: act?.time ?? '09:00',
                        name: act?.name ?? 'Hoạt động',
                        desc: act?.desc ?? 'Đang cập nhật mô tả.',
                        cost: Number(act?.cost ?? 0),
                        icon: act?.icon ?? 'mappin',
                        serviceId: typeof act?.serviceId === 'number' ? act.serviceId : null,
                    }))
                    : [],
            }))
            : [],
        recommendedServices: Array.isArray(raw?.recommendedServices)
            ? raw.recommendedServices.map((svc: any) => ({
                id: Number(svc?.id),
                title: svc?.title ?? 'Dịch vụ',
                provider: svc?.provider ?? 'Nhà cung cấp',
                rating: Number(svc?.rating ?? 0),
                reviews: Number(svc?.reviews ?? 0),
                price: Number(svc?.price ?? 0),
                image: svc?.image ?? 'https://picsum.photos/seed/fallback/400/250',
                category: svc?.category,
                category_name: svc?.category_name,
                location: svc?.location,
            }))
            : [],
        insights: Array.isArray(raw?.insights) ? raw.insights.filter(Boolean) : [],
    };
}

async function handleSubmit() {
    if (!ensureAuthenticated('tạo lịch trình bằng AI')) return;
    if (!prompt.value.trim()) {
        toast.error('Vui lòng nhập nhu cầu của bạn trước khi tạo lịch trình.');
        return;
    }

    plannerError.value = '';
    result.value = null;
    plannerSource.value = null;
    savedAt.value = null;
    loading.value = true;

    try {
        const response = await fetch('/customer/ai-planner/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(plannerPayload()),
        });

        if (!response.ok) {
            if (response.status === 429) {
                throw new Error('Bạn đang gửi yêu cầu quá nhanh. Vui lòng đợi một chút rồi thử lại.');
            }

            throw new Error('Không thể tạo lịch trình lúc này.');
        }

        const data = await response.json();
        applyResolvedInput(data.resolvedInput);
        result.value = hydratePlanResult(data.result);
        plannerSource.value = data.source ?? null;
        activeTab.value = 'itinerary';

        if (plannerSource.value === 'fallback') {
            toast.message('Đã chuyển sang gợi ý nội bộ do AI đang bận hoặc chưa sẵn sàng.');
        } else {
            toast.success('Lịch trình AI đã sẵn sàng.');
        }

        await nextTick();
        document.getElementById('ai-result')?.scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
        plannerError.value = error instanceof Error ? error.message : 'Không thể tạo lịch trình.';
        toast.error(plannerError.value);
    } finally {
        loading.value = false;
    }
}

function applyFieldUpdates(fieldUpdates: any) {
    if (typeof fieldUpdates?.location === 'string' && fieldUpdates.location.trim()) {
        location.value = fieldUpdates.location.trim();
    }

    if (typeof fieldUpdates?.duration === 'string' && fieldUpdates.duration.trim()) {
        duration.value = fieldUpdates.duration.trim();
    }

    if (typeof fieldUpdates?.budgetAmount === 'number' && Number.isFinite(fieldUpdates.budgetAmount)) {
        setBudgetAmount(fieldUpdates.budgetAmount);
    }

    if (typeof fieldUpdates?.numPeople === 'number' && Number.isFinite(fieldUpdates.numPeople)) {
        numPeople.value = Math.max(1, Math.min(20, fieldUpdates.numPeople));
    }

    if (typeof fieldUpdates?.prompt === 'string' && fieldUpdates.prompt.trim()) {
        prompt.value = fieldUpdates.prompt.trim();
    }
}

async function sendChat() {
    if (!chatInput.value.trim()) return;
    if (!ensureAuthenticated('chat với AI Planner')) return;

    const message = chatInput.value.trim();
    chatMessages.value.push({ role: 'user', text: message });
    chatInput.value = '';
    chatLoading.value = true;

    try {
        const response = await fetch('/customer/ai-planner/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                ...plannerPayload(),
                message,
            }),
        });

        if (!response.ok) {
            if (response.status === 429) {
                throw new Error('Bạn đang gửi tin nhắn quá nhanh. Hãy chậm lại một chút nhé.');
            }

            throw new Error('AI chat hiện chưa phản hồi được.');
        }

        const data = await response.json();
        applyResolvedInput(data.resolvedInput);
        applyFieldUpdates(data.fieldUpdates);
        chatMessages.value.push({
            role: 'ai',
            text: data.reply || 'Mình đã nhận thông tin. Bạn thử bấm tạo lịch trình để xem bản chi tiết nhé.',
        });

        if (data.source === 'fallback') {
            toast.message('Chat đang dùng gợi ý nội bộ.');
        }
    } catch (error) {
        chatMessages.value.push({
            role: 'ai',
            text: error instanceof Error ? error.message : 'Mình đang gặp trục trặc nhỏ, bạn thử lại sau một chút nhé.',
        });
    } finally {
        chatLoading.value = false;
    }
}

function saveResult() {
    if (!result.value) return;

    const payload = {
        savedAt: new Date().toISOString(),
        plan: result.value,
    };

    localStorage.setItem(storageKey.value, JSON.stringify(payload));
    savedAt.value = payload.savedAt;
    toast.success('Đã lưu lịch trình trên trình duyệt hiện tại.');
}

const activityIcon = (icon: string) => {
    const map: Record<string, any> = { bed: Bed, mappin: MapPin, food: Utensils, car: Car, coffee: Coffee };
    return map[icon] ?? MapPin;
};

const formatVND = (v: number) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Math.max(0, Number(v) || 0));
</script>

<template>
    <Head title="AI Lên Lịch Trình — Dalat Services" />

    <MarketplaceLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-emerald-950 to-stone-900">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wNSkiLz48L3N2Zz4=')] opacity-40"></div>
            <div class="pointer-events-none absolute -right-40 -top-40 size-[30rem] rounded-full bg-violet-500/15 blur-[120px]"></div>
            <div class="pointer-events-none absolute -bottom-20 left-1/4 size-[20rem] rounded-full bg-brand-surface0/15 blur-[100px]"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <nav class="mb-8 flex items-center gap-2 text-sm text-white/50">
                    <Link href="/" class="flex items-center gap-1 transition hover:text-white">
                        <Home class="size-3.5" /> Trang chủ
                    </Link>
                    <ChevronRight class="size-3.5" />
                    <span class="font-medium text-white">AI Planner</span>
                </nav>

                <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
                    <div class="text-white">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-violet-400/30 bg-violet-900/30 px-4 py-1.5 text-xs font-bold tracking-wide text-violet-300 backdrop-blur-md">
                            <Sparkles class="size-4" />
                            AI LÊN LỊCH TRÌNH & GỢI Ý DỊCH VỤ
                        </div>
                        <h1 class="text-4xl font-black tracking-tight sm:text-5xl lg:leading-[1.1]">
                            Chỉ cần mô tả nhu cầu,<br>
                            <span class="bg-gradient-to-r from-emerald-300 to-violet-300 bg-clip-text text-transparent">AI sẽ thiết kế lịch trình</span><br>
                            hoặc tìm dịch vụ cho bạn.
                        </h1>
                        <p class="mt-5 max-w-lg text-lg leading-relaxed text-stone-300">
                            Form bên phải sẽ gọi AI thật qua dữ liệu dịch vụ đang có trên hệ thống. Bạn có thể dùng nó để lên lịch trình du lịch hoặc tạo action plan cho các nhu cầu như thuê xe, gọi thợ, đặt dịch vụ.
                        </p>

                        <div class="mt-8 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-widest text-white/40">Nguồn gợi ý</p>
                                <p class="mt-2 text-sm font-semibold text-white">Dữ liệu dịch vụ thật</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-widest text-white/40">AI mode</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ plannerSource === 'fallback' ? 'Fallback nội bộ' : 'Gemini + fallback' }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-widest text-white/40">Lưu cục bộ</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ savedAt ? 'Đã có lịch trình lưu' : 'Sẵn sàng lưu' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
                        <form @submit.prevent="handleSubmit" class="space-y-5">
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-white/50">Bạn muốn đi đâu / Cần dịch vụ gì?</label>
                                <textarea
                                    v-model="prompt"
                                    rows="3"
                                    class="w-full resize-none rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white placeholder-white/30 outline-none transition focus:border-brand focus:bg-white/10"
                                    placeholder="VD: Tôi muốn đi Đà Lạt 3N2Đ cùng gia đình 4 người, thích cà phê và chụp ảnh..."
                                    required
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-white/40">Địa điểm</label>
                                    <div class="relative">
                                        <MapPin class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-white/30" />
                                        <select v-model="location" class="w-full appearance-none rounded-xl border border-white/10 bg-white/5 py-2.5 pl-9 pr-3 text-sm text-white outline-none focus:border-brand">
                                            <option value="" disabled hidden>-- Chọn địa điểm --</option>
                                            <option v-for="loc in locations" :key="loc" :value="loc" class="bg-stone-900">{{ loc }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-white/40">Thời gian</label>
                                    <div class="relative">
                                        <Calendar class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-white/30" />
                                        <select v-model="duration" class="w-full appearance-none rounded-xl border border-white/10 bg-white/5 py-2.5 pl-9 pr-3 text-sm text-white outline-none focus:border-brand">
                                            <option value="" disabled hidden>-- Thời gian --</option>
                                            <option v-for="d in durations" :key="d" :value="d" class="bg-stone-900">{{ d }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-white/40">Ngân sách</label>
                                    <div class="relative">
                                        <Wallet class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-white/30" />
                                        <input v-model="budget" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-9 pr-3 text-sm text-white placeholder-white/30 outline-none focus:border-brand" placeholder="VD: 5.000.000 (Có thể bỏ trống)" />
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="showAdvanced = !showAdvanced" class="flex items-center gap-1.5 text-xs font-medium text-white/50 transition hover:text-white/80">
                                <ChevronDown v-if="!showAdvanced" class="size-3.5" />
                                <ChevronUp v-else class="size-3.5" />
                                Tùy chọn nâng cao (số người, sở thích)
                            </button>

                            <div v-show="showAdvanced" class="space-y-4 rounded-xl border border-white/5 bg-white/5 p-4">
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-white/40">Số người</label>
                                    <div class="flex items-center gap-3">
                                        <Users class="size-4 text-white/30" />
                                        <input v-model.number="numPeople" type="number" min="1" max="20" class="w-20 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-center text-sm text-white outline-none focus:border-brand" />
                                        <span class="text-xs text-white/40">người</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-white/40">Sở thích</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="pref in prefOptions"
                                            :key="pref.value"
                                            type="button"
                                            @click="togglePref(pref.value)"
                                            class="rounded-full border px-3 py-1.5 text-xs font-medium transition"
                                            :class="preferences.includes(pref.value) ? 'border-brand bg-brand-surface0/20 text-brand' : 'border-white/10 text-white/50 hover:text-white/80'"
                                        >
                                            {{ pref.label }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="plannerError" class="rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                                {{ plannerError }}
                            </div>

                            <button
                                type="submit"
                                :disabled="loading"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-violet-500 py-4 text-sm font-bold text-white shadow-lg shadow-violet-500/20 transition-all hover:shadow-xl hover:shadow-violet-500/30 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <Loader2 v-if="loading" class="size-5 animate-spin" />
                                <Sparkles v-else class="size-5" />
                                {{ loading ? 'Đang phân tích yêu cầu...' : '✨ Tạo lịch trình bằng AI' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-10">
                    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-white/30">Mẫu câu hỏi gợi ý</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="sp in suggestedPrompts"
                            :key="sp"
                            @click="usePrompt(sp)"
                            class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-medium text-white/60 backdrop-blur transition hover:bg-white/10 hover:text-white"
                        >
                            "{{ sp }}"
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="result" id="ai-result" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-brand bg-brand-surface px-3 py-1 text-xs font-bold text-brand">
                        <Sparkles class="size-3.5" /> KẾT QUẢ GỢI Ý
                    </div>
                    <h2 class="text-2xl font-black tracking-tight text-stone-900 sm:text-3xl">{{ result.title }}</h2>
                    <p class="mt-1 text-sm text-stone-500">{{ result.subtitle }}</p>
                    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-stone-600">{{ result.summary }}</p>
                    <div v-if="result.insights.length > 0" class="mt-4 flex flex-wrap gap-2">
                        <span v-for="insight in result.insights" :key="insight" class="rounded-full bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600">
                            {{ insight }}
                        </span>
                    </div>
                    <p v-if="savedAt" class="mt-3 text-xs text-stone-400">Đã lưu lúc {{ new Date(savedAt).toLocaleString('vi-VN') }}</p>
                </div>
                <button class="inline-flex items-center gap-2 rounded-2xl border border-stone-300 bg-white px-5 py-3 text-sm font-semibold text-stone-700 shadow-sm transition hover:bg-stone-50" @click="saveResult">
                    Lưu lịch trình
                </button>
            </div>

            <div class="mb-8 flex gap-1 rounded-2xl border border-stone-200 bg-stone-100 p-1.5">
                <button
                    v-for="tab in [
                        { key: 'itinerary', label: '📋 Lịch trình chi tiết' },
                        { key: 'services', label: '🚀 Dịch vụ đề xuất' },
                        { key: 'budget', label: '💰 Dự toán chi phí' },
                    ]"
                    :key="tab.key"
                    @click="activeTab = tab.key as 'itinerary' | 'services' | 'budget'"
                    class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                    :class="activeTab === tab.key ? 'bg-white text-stone-900 shadow-sm' : 'text-stone-500 hover:text-stone-700'"
                >
                    {{ tab.label }}
                </button>
            </div>

            <div v-show="activeTab === 'itinerary'" class="space-y-8">
                <div v-for="day in result.days" :key="day.day" class="relative">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-violet-500 text-sm font-black text-white shadow-md">
                            {{ day.day }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-stone-900">Ngày {{ day.day }}: {{ day.title }}</h3>
                        </div>
                    </div>

                    <div class="ml-5 border-l-2 border-stone-200 pl-8">
                        <div v-for="(act, idx) in day.activities" :key="idx" class="relative mb-4 last:mb-0">
                            <div class="absolute -left-[2.55rem] top-3 size-3 rounded-full border-2 border-white shadow-sm" :class="act.serviceId ? 'bg-brand-surface0' : 'bg-stone-300'"></div>

                            <div class="rounded-2xl border p-4 transition-all hover:shadow-md" :class="act.serviceId ? 'border-brand bg-brand-surface/50' : 'border-stone-200 bg-white'">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-0.5 shrink-0 rounded-lg bg-stone-100 p-2">
                                            <component :is="activityIcon(act.icon)" class="size-4 text-stone-600" />
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-brand">{{ act.time }}</span>
                                                <span class="text-sm font-bold text-stone-900">{{ act.name }}</span>
                                            </div>
                                            <p class="mt-0.5 text-xs text-stone-500">{{ act.desc }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="whitespace-nowrap rounded-lg bg-stone-100 px-3 py-1 text-xs font-bold text-stone-700">{{ formatVND(act.cost) }}</span>
                                        <Link
                                            v-if="act.serviceId"
                                            :href="`/services/${act.serviceId}`"
                                            class="whitespace-nowrap rounded-full bg-brand px-4 py-2 text-xs font-bold text-white transition hover:bg-brand"
                                        >
                                            Đặt
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-show="activeTab === 'services'" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="svc in result.recommendedServices"
                    :key="svc.id"
                    :href="`/services/${svc.id}`"
                    class="group overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="relative h-40 overflow-hidden">
                        <img :src="svc.image" :alt="svc.title" class="size-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" referrerpolicy="no-referrer" />
                        <div class="absolute bottom-3 right-3 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-xs font-bold text-stone-900 shadow-sm backdrop-blur">
                            <Star class="size-3.5 fill-amber-400 text-amber-400" />
                            {{ Number(svc.rating).toFixed(1) }}
                            <span class="font-normal text-stone-400">({{ svc.reviews }})</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h4 class="text-sm font-bold text-stone-900 transition group-hover:text-brand">{{ svc.title }}</h4>
                        <p class="mt-1 flex items-center gap-1 text-xs text-stone-400">
                            <BadgeCheck class="size-3.5 text-brand" /> {{ svc.provider }}
                        </p>
                        <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-3">
                            <span class="text-lg font-black text-brand">{{ formatVND(svc.price) }}</span>
                            <span class="rounded-full bg-brand px-4 py-2 text-xs font-bold text-white">ĐẶT NGAY</span>
                        </div>
                    </div>
                </Link>
            </div>

            <div v-show="activeTab === 'budget'" class="mx-auto max-w-lg">
                <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                    <h3 class="mb-6 flex items-center gap-2 text-lg font-bold text-stone-900">
                        <Wallet class="size-5 text-brand" /> Dự toán chi phí
                    </h3>
                    <div class="space-y-4">
                        <div v-for="(val, key) in result.budgetBreakdown" :key="key" class="flex items-center justify-between border-b border-stone-100 pb-3">
                            <span class="text-sm text-stone-600 capitalize">
                                {{ key === 'accommodation' ? '🏨 Khách sạn' : key === 'transport' ? '🚗 Di chuyển' : key === 'sightseeing' ? '🎫 Vé tham quan' : key === 'food' ? '🍜 Ăn uống' : '📦 Khác' }}
                            </span>
                            <span class="text-sm font-bold text-stone-900">{{ formatVND(Number(val) || 0) }}</span>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between rounded-2xl bg-gradient-to-r from-emerald-50 to-violet-50 p-4">
                        <span class="text-sm font-bold text-stone-700">Tổng cộng</span>
                        <span class="text-xl font-black text-brand">{{ formatVND(result.totalBudget) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <Teleport to="body">
            <button
                v-if="!chatOpen"
                @click="chatOpen = true"
                class="fixed bottom-6 right-6 z-50 flex size-14 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-violet-600 text-white shadow-xl shadow-violet-500/30 transition-all hover:scale-110 hover:shadow-2xl"
            >
                <MessageCircle class="size-6" />
            </button>

            <Transition name="chat">
                <div v-if="chatOpen" class="fixed bottom-6 right-6 z-50 flex w-80 flex-col overflow-hidden rounded-[1.5rem] border border-stone-200 bg-white shadow-2xl sm:w-96">
                    <div class="flex items-center justify-between bg-gradient-to-r from-emerald-600 to-violet-600 px-5 py-4 text-white">
                        <div class="flex items-center gap-2">
                            <Bot class="size-5" />
                            <span class="text-sm font-bold">Trợ lý AI ServEasy</span>
                        </div>
                        <button @click="chatOpen = false" class="rounded-full p-1 transition hover:bg-white/20">
                            <X class="size-4" />
                        </button>
                    </div>

                    <div class="flex max-h-72 flex-1 flex-col gap-3 overflow-y-auto p-4">
                        <div v-for="(msg, i) in chatMessages" :key="i" :class="msg.role === 'ai' ? 'self-start' : 'self-end'">
                            <div
                                class="max-w-[85%] whitespace-pre-line rounded-2xl px-4 py-2.5 text-sm"
                                :class="msg.role === 'ai'
                                    ? 'rounded-tl-sm bg-stone-100 text-stone-700'
                                    : 'rounded-tr-sm bg-gradient-to-br from-emerald-500 to-violet-500 text-white'"
                            >
                                {{ msg.text }}
                            </div>
                        </div>
                        <div v-if="chatLoading" class="self-start">
                            <div class="flex items-center gap-2 rounded-2xl rounded-tl-sm bg-stone-100 px-4 py-3 text-sm text-stone-500">
                                <Loader2 class="size-4 animate-spin" /> Đang suy nghĩ...
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-stone-200 p-3">
                        <form @submit.prevent="sendChat" class="flex items-center gap-2">
                            <input
                                v-model="chatInput"
                                type="text"
                                placeholder="Nhập tin nhắn..."
                                class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm outline-none transition focus:border-brand"
                            />
                            <button type="submit" class="flex size-10 items-center justify-center rounded-xl bg-brand text-white transition hover:bg-brand">
                                <Send class="size-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </MarketplaceLayout>
</template>

<style scoped>
.chat-enter-active,
.chat-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.chat-enter-from,
.chat-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}
</style>
