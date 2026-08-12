<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

const passwordValue = ref('');

const passwordStrength = computed(() => {
    let score = 0;
    if (!passwordValue.value) return 0;
    if (passwordValue.value.length >= 8) score += 1;
    if (/[A-Z]/.test(passwordValue.value)) score += 1;
    if (/[0-9]/.test(passwordValue.value)) score += 1;
    if (/[^A-Za-z0-9]/.test(passwordValue.value)) score += 1;
    return score;
});

const strengthLabels = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
const strengthClasses = [
    'bg-stone-200',
    'bg-red-500',
    'bg-amber-500',
    'bg-brand-surface0',
    'bg-emerald-500'
];
</script>

<template>
    <AuthBase
        title="Tạo tài khoản mới"
        description="Bắt đầu dùng hệ thống để tìm dịch vụ, đặt lịch và theo dõi các giao dịch của bạn."
    >
        <Head title="Đăng ký" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Họ và tên</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Nguyen Van A"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="ban@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-3">
                    <Label>Đăng ký với vai trò</Label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="group flex cursor-pointer gap-3 rounded-2xl border border-stone-200 bg-white p-4 transition hover:border-brand-surface0/60 hover:bg-stone-50 has-checked:border-brand-surface0 has-checked:bg-brand-surface0/10">
                            <input
                                type="radio"
                                name="role"
                                value="customer"
                                required
                                checked
                                class="mt-1 size-4 accent-brand-surface0"
                                :tabindex="3"
                            />
                            <span class="grid gap-1">
                                <span class="font-medium text-stone-900">Khách hàng</span>
                                <span class="text-sm text-stone-500">Tìm kiếm dịch vụ, đặt lịch và theo dõi giao dịch.</span>
                            </span>
                        </label>

                        <label class="group flex cursor-pointer gap-3 rounded-2xl border border-stone-200 bg-white p-4 transition hover:border-brand-surface0/60 hover:bg-stone-50 has-checked:border-brand-surface0 has-checked:bg-brand-surface0/10">
                            <input
                                type="radio"
                                name="role"
                                value="provider"
                                required
                                class="mt-1 size-4 accent-brand-surface0"
                                :tabindex="4"
                            />
                            <span class="grid gap-1">
                                <span class="font-medium text-stone-900">Nhà cung cấp</span>
                                <span class="text-sm text-stone-500">Đăng dịch vụ, quản lý lịch đặt và hồ sơ kinh doanh.</span>
                            </span>
                        </label>
                    </div>
                    <InputError :message="errors.role" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Mật khẩu</Label>
                    <PasswordInput
                        id="password"
                        required
                        minlength="1"
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Tạo mật khẩu"
                        @input="(e: Event) => passwordValue = (e.target as HTMLInputElement).value"
                    />
                    <InputError :message="errors.password" />

                    <!-- Thêm thanh đánh giá độ mạnh mật khẩu -->
                    <div class="mt-1 flex flex-col gap-1.5" v-if="passwordValue">
                        <div class="flex h-1.5 w-full gap-1 overflow-hidden rounded-full">
                            <div 
                                v-for="i in 4" 
                                :key="'strength-'+i"
                                :class="[
                                    'h-full flex-1 transition-colors duration-300',
                                    passwordStrength >= i ? strengthClasses[passwordStrength] : 'bg-stone-200'
                                ]"
                            ></div>
                        </div>
                        <p class="text-xs text-stone-500">
                            Độ mạnh: <span class="font-medium inline-block min-w-16">{{ strengthLabels[passwordStrength] }}</span>
                        </p>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Xác nhận mật khẩu</Label>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        minlength="1"
                        :tabindex="6"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Nhập lại mật khẩu"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="7"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Tạo tài khoản
                </Button>
            </div>

            <div class="text-center text-sm text-stone-500">
                Đã có tài khoản?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="8"
                    >Đăng nhập</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
