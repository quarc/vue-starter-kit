<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem, UIConfigLayout } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    width?: UIConfigLayout['app_width'];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    width: 'full',
});
</script>

<template>
    <AppShell variant="sidebar" :width="props.width">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
