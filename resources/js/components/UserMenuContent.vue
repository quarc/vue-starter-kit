<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';
import { index as notificationsIndex } from '@/routes/notifications';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import {
    Bell,
    LogOut,
    Monitor,
    Moon,
    Palette,
    Settings,
    Sun,
} from 'lucide-vue-next';

interface Props {
    user: User;
}

const { appearance, updateAppearance } = useAppearance();

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-2 text-sm">
                <Palette class="mr-2 h-4 w-4 text-muted-foreground" />
                {{ $t('Theme') }}
            </div>
            <div
                class="flex items-center gap-1 rounded-full border bg-muted p-0.5"
            >
                <button
                    @click.stop="updateAppearance('light')"
                    class="flex size-6 items-center justify-center rounded-full transition-all hover:text-foreground"
                    :class="
                        appearance === 'light'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground'
                    "
                >
                    <Sun class="size-3.5" />
                </button>
                <button
                    @click.stop="updateAppearance('dark')"
                    class="flex size-6 items-center justify-center rounded-full transition-all hover:text-foreground"
                    :class="
                        appearance === 'dark'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground'
                    "
                >
                    <Moon class="size-3.5" />
                </button>
                <button
                    @click.stop="updateAppearance('system')"
                    class="flex size-6 items-center justify-center rounded-full transition-all hover:text-foreground"
                    :class="
                        appearance === 'system'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground'
                    "
                >
                    <Monitor class="size-3.5" />
                </button>
            </div>
        </div>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="notificationsIndex()"
                prefetch
                as="button"
            >
                <Bell class="mr-2 h-4 w-4" />
                {{ $t('Notifications') }}
                <span
                    v-if="(user.unread_notifications_count ?? 0) > 0"
                    class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-medium text-primary-foreground"
                >
                    {{
                        (user.unread_notifications_count ?? 0) > 99
                            ? '99+'
                            : user.unread_notifications_count
                    }}
                </span>
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-4 w-4" />
                {{ $t('Settings') }}
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full"
            :href="logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            {{ $t('Log out') }}
        </Link>
    </DropdownMenuItem>
</template>
