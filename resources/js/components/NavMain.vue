<script setup lang="ts">
import NavMenuItem from '@/components/NavMenuItem.vue';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
} from '@/components/ui/sidebar';
import { convertNavigationItem } from '@/composables/useNavigation';
import type { NavItem, NavigationSection } from '@/types';
import { computed } from 'vue';

interface Props {
    items?: NavItem[];
    sections?: NavigationSection[];
}

const props = defineProps<Props>();

const sections = computed(() => {
    if (props.sections) {
        return props.sections.map((section: NavigationSection) => ({
            label: section.label,
            items: section.items.map(convertNavigationItem),
        }));
    }

    if (props.items?.length) {
        return [
            {
                label: 'Platform',
                items: props.items,
            },
        ];
    }

    return [];
});
</script>

<template>
    <template v-for="section in sections" :key="section.label">
        <SidebarGroup class="px-2 py-0">
            <SidebarGroupLabel>{{ section.label }}</SidebarGroupLabel>
            <SidebarMenu>
                <NavMenuItem
                    v-for="item in section.items"
                    :key="item.title"
                    :item="item"
                />
            </SidebarMenu>
        </SidebarGroup>
    </template>
</template>
