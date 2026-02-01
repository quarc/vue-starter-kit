<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
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
    <AppShell variant="header" class="flex-col">
        <AppHeader :breadcrumbs="breadcrumbs" :width="props.width" />
        <AppContent variant="header" :width="props.width">
            <slot />
        </AppContent>
    </AppShell>
</template>
