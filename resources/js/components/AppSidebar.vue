<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import type {
    NavigationSection,
    UIConfigLayout,
    UIConfigSidebar,
    UIConfigToggles,
} from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

const sidebar = computed(() => usePage().props.sidebar as UIConfigSidebar);
const navigation = computed(
    () => usePage().props.navigation?.sidebar as NavigationSection[],
);
const toggles = computed(() => usePage().props.toggles as UIConfigToggles);
const layout = computed(() => usePage().props.layout as UIConfigLayout);
useSidebar();
</script>

<template>
    <Sidebar
        collapsible="icon"
        :variant="sidebar.variant"
        class="!right-auto !left-auto"
    >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link
                            href="/dashboard"
                            class="relative flex items-center gap-x-2 overflow-visible"
                        >
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
                            >
                                <AppLogoIcon
                                    class="size-5 fill-current text-sidebar-primary-foreground"
                                />
                            </div>
                            <div
                                class="grid flex-1 text-left text-sm leading-tight"
                            >
                                <span class="truncate font-semibold">{{
                                    layout.title
                                }}</span>
                                <span
                                    v-if="toggles?.show_slogan"
                                    class="truncate text-[10px] font-normal text-muted-foreground"
                                    >{{ layout.slogan }}</span
                                >
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain v-if="navigation" :sections="navigation" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter
                :items="[
                    {
                        title: $t('Repository'),
                        href: 'https://github.com/laravel/vue-starter-kit',
                        icon: Folder,
                    },
                    {
                        title: $t('Documentation'),
                        href: 'https://laravel.com/docs/starter-kits#vue',
                        icon: BookOpen,
                    },
                ]"
            />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
