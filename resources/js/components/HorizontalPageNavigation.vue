<script setup lang="ts">
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <nav
        class="mb-6 flex space-x-6 overflow-x-auto border-b border-sidebar-border/70"
    >
        <Link
            v-for="item in items"
            :key="toUrl(item.href)"
            :href="item.href"
            :class="[
                'flex items-center gap-2 border-b-2 pb-3 text-sm font-medium whitespace-nowrap transition-colors',
                urlIsActive(item.href, currentPath)
                    ? 'border-primary text-primary'
                    : 'border-transparent text-muted-foreground hover:border-muted-foreground/50 hover:text-foreground',
            ]"
        >
            <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
            {{ item.title }}
        </Link>
    </nav>
</template>
