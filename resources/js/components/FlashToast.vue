<script setup lang="ts">
import { watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Toaster, toast } from 'vue-sonner';

const page = usePage();

function checkFlash() {
    const flash = (page.props as any).flash;
    if (flash?.success) toast.success(flash.success);
    else if (flash?.error) toast.error(flash.error);

    const errors = (page.props as any).errors;
    if (errors && Object.keys(errors).length > 0) {
        // Find the first error string and show it
        const firstErrorKey = Object.keys(errors)[0];
        toast.error('Vui lòng kiểm tra lại thông tin', { description: errors[firstErrorKey] });
    }
}

onMounted(checkFlash);
watch(() => (page.props as any).flash, checkFlash, { deep: true });
watch(() => (page.props as any).errors, checkFlash, { deep: true });
</script>

<template>
    <Toaster richColors position="bottom-right" />
</template>
