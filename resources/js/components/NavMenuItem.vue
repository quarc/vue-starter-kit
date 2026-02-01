<script setup lang="ts">
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import type { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

interface Props {
    item: NavItem;
}

defineProps<Props>();

const page = usePage();
</script>

<template>
    <SidebarMenuItem>
        <!-- Separator removed -->

        <!-- Item з children -->
        <Collapsible
            v-if="item.children?.length"
            :default-open="
                item.children?.some((child) =>
                    urlIsActive(child.href, page.url),
                )
            "
            class="group/collapsible"
        >
            <CollapsibleTrigger as-child>
                <SidebarMenuButton
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <component v-if="item.icon" :is="item.icon" />
                    <span>{{ item.title }}</span>
                    <ChevronRight
                        class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-90"
                    />
                    <SidebarMenuBadge v-if="item.badge">
                        {{ item.badge }}
                    </SidebarMenuBadge>
                </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent>
                <SidebarMenuSub>
                    <SidebarMenuSubItem
                        v-for="child in item.children"
                        :key="child.title"
                    >
                        <SidebarMenuSubButton
                            as-child
                            :is-active="urlIsActive(child.href, page.url)"
                        >
                            <Link :href="child.href">
                                <span>{{ child.title }}</span>
                                <SidebarMenuBadge v-if="child.badge">
                                    {{ child.badge }}
                                </SidebarMenuBadge>
                            </Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </CollapsibleContent>
        </Collapsible>

        <!-- Simple item -->
        <SidebarMenuButton
            v-else
            as-child
            :is-active="urlIsActive(item.href, page.url)"
            :tooltip="item.title"
        >
            <Link :href="item.href">
                <component v-if="item.icon" :is="item.icon" />
                <span>{{ item.title }}</span>
                <SidebarMenuBadge v-if="item.badge">
                    {{ item.badge }}
                </SidebarMenuBadge>
            </Link>
        </SidebarMenuButton>
    </SidebarMenuItem>
</template>
