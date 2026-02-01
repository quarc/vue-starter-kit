<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    width?: 'container' | 'full';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    width: 'container',
});

const maxWidthClass = computed(() => {
    if (props.class?.includes('max-w-')) return '';
    return props.width === 'full' ? 'max-w-full' : 'max-w-7xl';
});
</script>

<template>
    <SidebarInset v-if="variant === 'sidebar'" :class="props.class">
        <slot />
    </SidebarInset>
    <main
        v-else
        :class="
            cn(
                'mx-auto flex h-full w-full flex-1 flex-col gap-4 rounded-xl',
                maxWidthClass,
                props.class,
            )
        "
    >
        <slot />
    </main>
</template>
