<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <nav class="flex flex-col space-y-1 space-x-0">
        <Button
            v-for="item in items"
            :key="toUrl(item.href)"
            variant="ghost"
            :class="[
                'w-full justify-start',
                { 'bg-muted': urlIsActive(item.href, currentPath) },
            ]"
            as-child
        >
            <Link :href="item.href">
                <component :is="item.icon" class="h-4 w-4" />
                {{ item.title }}
            </Link>
        </Button>
    </nav>
</template>
