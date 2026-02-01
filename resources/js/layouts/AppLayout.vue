<script setup lang="ts">
import type { BreadcrumbItem, UIConfigLayout } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, defineAsyncComponent } from 'vue';

const layout = computed(() => usePage().props.layout as UIConfigLayout);
const layoutType = computed(() => layout.value.navigation || 'sidebar');

const AppLayout = computed(() =>
    defineAsyncComponent(() =>
        layoutType.value === 'topbar'
            ? import('@/layouts/app/AppHeaderLayout.vue')
            : import('@/layouts/app/AppSidebarLayout.vue'),
    ),
);

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :width="layout.app_width" :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
