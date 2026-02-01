<script setup lang="ts">
import HorizontalPageNavigation from '@/components/HorizontalPageNavigation.vue';
import { Separator } from '@/components/ui/separator';
import VerticalPageNavigation from '@/components/VerticalPageNavigation.vue';
import { type NavItem, type UIConfigLayout } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const layout = computed(() => usePage().props.layout as UIConfigLayout);
</script>

<template>
    <div
        class="flex flex-col"
        :class="[
            layout.settings_navigation === 'vertical'
                ? 'lg:flex-row lg:space-x-12'
                : 'items-center space-y-6',
        ]"
    >
        <aside
            v-if="layout.settings_navigation === 'vertical'"
            class="w-full max-w-xl lg:w-48"
        >
            <VerticalPageNavigation :items="items" />
        </aside>
        <div v-else class="w-full">
            <HorizontalPageNavigation :items="items" />
        </div>

        <Separator
            v-if="layout.settings_navigation === 'vertical'"
            class="my-6 lg:hidden"
        />

        <div
            :class="[
                layout.settings_navigation === 'vertical'
                    ? 'flex-1 md:max-w-2xl'
                    : 'w-full max-w-xl',
            ]"
        >
            <slot />
        </div>
    </div>
</template>
