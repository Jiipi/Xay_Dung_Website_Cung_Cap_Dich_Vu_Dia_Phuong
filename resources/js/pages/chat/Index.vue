<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { MessageCircle, Send, ArrowLeft, Search, User as UserIcon } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import ProviderLayout from '@/layouts/ProviderLayout.vue';

interface OtherUser {
    id: number;
    name: string;
    avatar: string | null;
}

interface ConversationItem {
    id: number;
    other_user: OtherUser;
    last_message: string | null;
    last_message_time: string | null;
    unread_count: number;
}

interface MessageItem {
    id: number;
    conversation_id: number;
    sender_id: number;
    sender_name: string;
    sender_avatar: string | null;
    content: string;
    is_read: boolean;
    created_at: string;
}

const props = defineProps<{
    conversations: ConversationItem[];
    authUserId: number;
}>();

const page = usePage();
const layoutComponent = computed(() => {
    const role = page.props.auth?.role;
    if (role === 'Nhà cung cấp') return ProviderLayout;
    if (role === 'Admin') return AdminLayout;
    return CustomerLayout;
});

const activeConversationId = ref<number | null>(null);
const messages = ref<MessageItem[]>([]);
const newMessage = ref('');
const isSending = ref(false);
const isLoadingMessages = ref(false);
const searchQuery = ref('');
const otherUser = ref<OtherUser | null>(null);
const messagesContainer = ref<HTMLElement | null>(null);
const mobileShowChat = ref(false);

const filteredConversations = computed(() => {
    if (!searchQuery.value) return props.conversations;
    const q = searchQuery.value.toLowerCase();
    return props.conversations.filter(c =>
        c.other_user.name.toLowerCase().includes(q)
    );
});

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
}

function appendMessage(message: MessageItem) {
    if (messages.value.some((item) => item.id === message.id)) return;

    messages.value.push(message);
    scrollToBottom();
}

async function selectConversation(conv: ConversationItem) {
    activeConversationId.value = conv.id;
    otherUser.value = conv.other_user;
    isLoadingMessages.value = true;
    mobileShowChat.value = true;

    try {
        const res = await fetch(`/chat/${conv.id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        const data = await res.json();
        messages.value = data.messages;
        otherUser.value = data.other_user;
        scrollToBottom();
    } catch (e) {
        console.error('Failed to load messages', e);
    } finally {
        isLoadingMessages.value = false;
    }
}

async function sendMessage() {
    if (!newMessage.value.trim() || isSending.value || !activeConversationId.value) return;

    isSending.value = true;
    const content = newMessage.value.trim();
    newMessage.value = '';

    try {
        const res = await fetch(`/chat/${activeConversationId.value}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ content }),
        });
        const msg = await res.json();
        appendMessage(msg);
        scrollToBottom();
    } catch (e) {
        console.error('Failed to send message', e);
        newMessage.value = content; // restore on failure
    } finally {
        isSending.value = false;
    }
}

function formatTime(iso: string) {
    const d = new Date(iso);
    return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(iso: string) {
    const d = new Date(iso);
    const today = new Date();
    if (d.toDateString() === today.toDateString()) return 'Hôm nay';
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    if (d.toDateString() === yesterday.toDateString()) return 'Hôm qua';
    return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function shouldShowDateHeader(index: number): boolean {
    if (index === 0) return true;
    const curr = new Date(messages.value[index].created_at).toDateString();
    const prev = new Date(messages.value[index - 1].created_at).toDateString();
    return curr !== prev;
}

let stopConversationWatcher: (() => void) | null = null;

onMounted(() => {
    const echo = (window as any).Echo;
    if (!echo) return;

    stopConversationWatcher = watch(activeConversationId, (newId, oldId) => {
        if (oldId) echo.leave(`conversation.${oldId}`);
        if (newId) {
            echo.private(`conversation.${newId}`)
                .listen('MessageSent', (event: MessageItem) => {
                    appendMessage(event);
                });
        }
    }, { immediate: true });
});

onUnmounted(() => {
    stopConversationWatcher?.();
    const echo = (window as any).Echo;
    if (echo && activeConversationId.value) {
        echo.leave(`conversation.${activeConversationId.value}`);
    }
});
</script>

<template>
    <Head title="Tin nhắn" />

    <component :is="layoutComponent">
        <div class="mx-auto max-w-7xl px-0 sm:px-4 lg:px-8">
            <div class="flex h-[calc(100vh-80px)] overflow-hidden rounded-none sm:rounded-2xl border border-stone-200 bg-white shadow-sm sm:my-6 sm:h-[calc(100vh-120px)]">

                <!-- Sidebar: Conversation List -->
                <div
                    class="w-full flex-shrink-0 border-r border-stone-100 bg-stone-50/50 sm:w-80 lg:w-96"
                    :class="{ 'hidden sm:block': mobileShowChat }"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                        <h1 class="text-lg font-bold text-stone-900">Tin nhắn</h1>
                        <MessageCircle class="size-5 text-stone-400" />
                    </div>

                    <!-- Search -->
                    <div class="px-4 py-3">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tìm kiếm cuộc trò chuyện..."
                                class="w-full rounded-xl border border-stone-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>
                    </div>

                    <!-- List -->
                    <div class="overflow-y-auto" style="max-height: calc(100% - 130px);">
                        <div v-if="filteredConversations.length === 0" class="px-6 py-16 text-center">
                            <MessageCircle class="mx-auto size-10 text-stone-300" />
                            <p class="mt-3 text-sm text-stone-400">Chưa có cuộc trò chuyện nào</p>
                        </div>

                        <button
                            v-for="conv in filteredConversations"
                            :key="conv.id"
                            @click="selectConversation(conv)"
                            class="flex w-full items-center gap-3 px-5 py-3.5 text-left transition-colors hover:bg-blue-50/60"
                            :class="{ 'bg-blue-50 border-r-2 border-blue-500': activeConversationId === conv.id }"
                        >
                            <div class="relative flex-shrink-0">
                                <img
                                    v-if="conv.other_user.avatar"
                                    :src="conv.other_user.avatar"
                                    class="size-11 rounded-full object-cover ring-2 ring-white"
                                    referrerpolicy="no-referrer"
                                />
                                <div v-else class="flex size-11 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 ring-2 ring-white">
                                    <UserIcon class="size-5 text-white" />
                                </div>
                                <span
                                    v-if="conv.unread_count > 0"
                                    class="absolute -right-1 -top-1 flex size-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white"
                                >
                                    {{ conv.unread_count > 9 ? '9+' : conv.unread_count }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="truncate text-sm font-semibold text-stone-900">{{ conv.other_user.name }}</span>
                                    <span class="text-[10px] text-stone-400 flex-shrink-0 ml-2">{{ conv.last_message_time }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-xs" :class="conv.unread_count > 0 ? 'font-medium text-stone-700' : 'text-stone-400'">
                                    {{ conv.last_message || 'Chưa có tin nhắn' }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div
                    class="flex flex-1 flex-col"
                    :class="{ 'hidden sm:flex': !mobileShowChat }"
                >
                    <!-- Empty State -->
                    <div v-if="!activeConversationId" class="flex flex-1 flex-col items-center justify-center text-center">
                        <div class="flex size-20 items-center justify-center rounded-full bg-blue-50">
                            <MessageCircle class="size-10 text-blue-400" />
                        </div>
                        <h2 class="mt-5 text-xl font-bold text-stone-900">Chọn cuộc trò chuyện</h2>
                        <p class="mt-2 max-w-sm text-sm text-stone-400">Chọn một cuộc hội thoại từ danh sách bên trái để bắt đầu nhắn tin với nhà cung cấp hoặc khách hàng.</p>
                    </div>

                    <!-- Active Chat -->
                    <template v-else>
                        <!-- Chat Header -->
                        <div class="flex items-center gap-3 border-b border-stone-100 px-5 py-3.5 bg-white/80 backdrop-blur-sm">
                            <button @click="mobileShowChat = false" class="sm:hidden rounded-lg p-1.5 text-stone-500 hover:bg-stone-100">
                                <ArrowLeft class="size-5" />
                            </button>
                            <img
                                v-if="otherUser?.avatar"
                                :src="otherUser.avatar"
                                class="size-10 rounded-full object-cover"
                                referrerpolicy="no-referrer"
                            />
                            <div v-else class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500">
                                <UserIcon class="size-5 text-white" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-stone-900">{{ otherUser?.name }}</p>
                                <p class="text-[11px] text-emerald-500 font-medium">Đang hoạt động</p>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto px-5 py-4 space-y-1 bg-stone-50/30">
                            <div v-if="isLoadingMessages" class="flex items-center justify-center py-20">
                                <div class="size-8 animate-spin rounded-full border-4 border-blue-200 border-t-blue-500"></div>
                            </div>

                            <template v-else>
                                <div v-if="messages.length === 0" class="py-20 text-center">
                                    <p class="text-sm text-stone-400">Chưa có tin nhắn. Hãy gửi lời chào!</p>
                                </div>

                                <template v-for="(msg, idx) in messages" :key="msg.id">
                                    <!-- Date Separator -->
                                    <div v-if="shouldShowDateHeader(idx)" class="flex items-center gap-3 py-3">
                                        <div class="h-px flex-1 bg-stone-200"></div>
                                        <span class="text-[10px] font-medium text-stone-400 uppercase tracking-wider">{{ formatDate(msg.created_at) }}</span>
                                        <div class="h-px flex-1 bg-stone-200"></div>
                                    </div>

                                    <!-- Message Bubble -->
                                    <div
                                        class="flex items-end gap-2"
                                        :class="msg.sender_id === authUserId ? 'justify-end' : 'justify-start'"
                                    >
                                        <!-- Other user avatar -->
                                        <img
                                            v-if="msg.sender_id !== authUserId && msg.sender_avatar"
                                            :src="msg.sender_avatar"
                                            class="mb-1 size-7 flex-shrink-0 rounded-full object-cover"
                                            referrerpolicy="no-referrer"
                                        />
                                        <div
                                            v-else-if="msg.sender_id !== authUserId"
                                            class="mb-1 flex size-7 flex-shrink-0 items-center justify-center rounded-full bg-stone-300"
                                        >
                                            <UserIcon class="size-3.5 text-white" />
                                        </div>

                                        <div
                                            class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm"
                                            :class="msg.sender_id === authUserId
                                                ? 'bg-blue-600 text-white rounded-br-md'
                                                : 'bg-white text-stone-800 border border-stone-100 rounded-bl-md'"
                                        >
                                            <p class="whitespace-pre-wrap break-words">{{ msg.content }}</p>
                                            <p
                                                class="mt-1 text-[10px]"
                                                :class="msg.sender_id === authUserId ? 'text-blue-200' : 'text-stone-400'"
                                            >
                                                {{ formatTime(msg.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <!-- Input -->
                        <div class="border-t border-stone-100 bg-white px-4 py-3">
                            <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                                <input
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Nhập tin nhắn..."
                                    class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-blue-300 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                    @keydown.enter.prevent="sendMessage"
                                    :disabled="isSending"
                                />
                                <button
                                    type="submit"
                                    :disabled="!newMessage.trim() || isSending"
                                    class="flex size-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-40"
                                >
                                    <Send class="size-5" :class="{ 'animate-pulse': isSending }" />
                                </button>
                            </form>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </component>
</template>
