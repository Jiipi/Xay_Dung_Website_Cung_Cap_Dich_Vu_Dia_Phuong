<script lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import ProviderLayout from '@/layouts/ProviderLayout.vue';
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { defineComponent, h } from 'vue';

export default defineComponent({
    layout: (createElement, page) => {
        const role = page.props.auth?.role;
        let LayoutComponent = CustomerLayout;
        if (role === 'Admin') LayoutComponent = AdminLayout;
        else if (role === 'Nhà cung cấp') LayoutComponent = ProviderLayout;
        return createElement(LayoutComponent, () => page);
    }
});
</script>
<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
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
</script>

<template>
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
    </template>
