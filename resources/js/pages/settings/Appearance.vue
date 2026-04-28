<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import MarketplaceLayout from '@/layouts/MarketplaceLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import type { BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: edit(),
    },
];

const page = usePage();
const layoutRole = computed(() => {
    const role = page.props.auth?.role;
    if (role === 'Admin') return 'admin';
    if (role === 'Nhà cung cấp') return 'provider';
    return 'customer';
});
</script>

<template>
    <MarketplaceLayout :role="layoutRole">
        <Head title="Appearance settings" />

        <h1 class="sr-only">Appearance settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Appearance settings"
                    description="Update your account's appearance settings"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </MarketplaceLayout>
</template>
