<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import PageNavigation from '@/components/PageNavigation.vue';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Archive, Bell, type LucideIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
const page = usePage();

// Map icon names to components
const iconMap: Record<string, LucideIcon> = {
    bell: Bell,
    archive: Archive,
};

interface NotificationsNavItem {
    name: string;
    label: string;
    icon: string | null;
    route: string | null;
    children: NotificationsNavItem[];
    badge: string | number | null;
    description: string | null;
}

interface NotificationsNavigation {
    label: string;
    items: NotificationsNavItem[];
}

const sidebarNavItems = computed<NavItem[]>(() => {
    const nav = page.props.notificationsNavigation as NotificationsNavigation;

    if (!nav?.items) {
        return [];
    }

    return nav.items.map((item) => ({
        title: item.label,
        href: item.route ?? '',
        icon: item.icon ? iconMap[item.icon] : undefined,
    }));
});
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            :title="$t('Notifications')"
            :description="$t('View and manage your notifications')"
        />

        <PageNavigation :items="sidebarNavItems">
            <section class="max-w-xl space-y-12">
                <slot />
            </section>
        </PageNavigation>
    </div>
</template>
