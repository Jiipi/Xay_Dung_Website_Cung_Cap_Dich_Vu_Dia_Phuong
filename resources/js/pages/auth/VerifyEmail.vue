<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Xác minh email"
        description="Vui lòng mở email và bấm vào liên kết xác minh để kích hoạt tài khoản của bạn."
    >
        <Head title="Xác minh email" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700"
        >
            Một liên kết xác minh mới đã được gửi đến email bạn đã dùng khi đăng ký.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <Spinner v-if="processing" />
                Gửi lại email xác minh
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                Đăng xuất
            </TextLink>
        </Form>
    </AuthLayout>
</template>
