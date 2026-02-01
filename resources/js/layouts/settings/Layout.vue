<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import PageNavigation from '@/components/PageNavigation.vue';
import { type NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import {
    CalendarClock,
    Globe,
    Lock,
    ShieldCheck,
    User,
    type LucideIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
const page = usePage();

// Map icon names to components
const iconMap: Record<string, LucideIcon> = {
    user: User,
    lock: Lock,
    'shield-check': ShieldCheck,
    globe: Globe,
    'calendar-clock': CalendarClock,
};

interface SettingsNavItem {
    name: string;
    label: string;
    icon: string | null;
    route: string | null;
    children: SettingsNavItem[];
    badge: string | number | null;
    description: string | null;
}

interface SettingsNavigation {
    label: string;
    items: SettingsNavItem[];
}

const sidebarNavItems = computed<NavItem[]>(() => {
    const nav = page.props.settingsNavigation as SettingsNavigation;

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
            :title="$t('Settings')"
            :description="$t('Manage your profile and account settings')"
        />

        <PageNavigation :items="sidebarNavItems">
            <section class="max-w-xl space-y-12">
                <slot />
            </section>
        </PageNavigation>
    </div>
</template>
