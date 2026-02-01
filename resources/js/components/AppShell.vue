<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';

interface Props {
    variant?: 'header' | 'sidebar';
    width?: 'container' | 'full';
}

const props = defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>

    <div v-else class="flex min-h-screen w-full flex-col bg-sidebar">
        <div
            class="mx-auto flex w-full flex-1"
            :class="props.width === 'full' ? 'max-w-full' : 'max-w-7xl'"
        >
            <SidebarProvider :default-open="isOpen">
                <slot />
            </SidebarProvider>
        </div>
    </div>
</template>
