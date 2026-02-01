<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import AppLayout from '@/layouts/AppLayout.vue';
import NotificationsLayout from '@/layouts/notifications/Layout.vue';
import { formatDate } from '@/lib/datetime';
import { index, read, unread } from '@/routes/notifications';
import type { Notification } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import * as LucideIcons from 'lucide-vue-next';
import {
    Archive,
    Bell,
    Check,
    CheckCheck,
    Clock,
    Laptop,
    Loader2,
    MapPin,
    Smartphone,
    Tablet,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    notifications: {
        data: Notification[];
        links?: Record<string, unknown>;
        next_page_url?: string;
    };
    counts: {
        total: number;
        unread: number;
        archived: number;
    };
}>();

const { t: $t, locale } = useI18n();
const page = usePage();

// Local state for counts to allow optimistic updates
const localCounts = ref({ ...props.counts });

watch(
    () => props.counts,
    (newCounts) => {
        localCounts.value = { ...newCounts };
    },
    { deep: true },
);

// User Settings
const user = computed(() => page.props.auth.user);
const timezone = computed(
    () => user.value?.timezone || page.props.timezone || 'UTC',
);
const dateFormat = computed(() => user.value?.date_format || 'd/m/Y');
const timeFormat = computed(() => user.value?.time_format || 'H:i');

// Breadcrumb
const breadcrumbItems = [{ title: $t('Notifications'), href: index().url }];

const unreadCount = computed(() => localCounts.value?.unread ?? 0);

// Date Helpers
const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = date.getTime() - now.getTime();
    const diffMins = Math.trunc(diffMs / 60000);

    // Relative for very recent (< 1 hour)
    if (diffMins > -60 && diffMins <= 0) {
        const rtf = new Intl.RelativeTimeFormat(
            (page.props.locale as string) || 'en',
            { numeric: 'auto', style: 'short' },
        );
        return rtf.format(diffMins, 'minute');
    }

    const timePart = formatDate(
        date,
        timeFormat.value,
        locale.value,
        timezone.value,
    );

    // Date + Time
    const datePart = formatDate(
        date,
        dateFormat.value,
        locale.value,
        timezone.value,
    );
    return `${datePart} ${timePart}`;
};

// Icons
const getIcon = (name: string) => {
    const icon = (LucideIcons as any)[name];
    return icon || LucideIcons.Bell;
};

type NotificationMeta = {
    ip: string | null;
    platform: string | null;
    device: 'mobile' | 'tablet' | 'desktop' | null;
};

const getNotificationMeta = (notification: Notification): NotificationMeta => {
    const meta = (notification.meta ?? {}) as Record<string, any>;
    const agent = (meta.agent ?? {}) as Record<string, any>;
    const device = agent.is_mobile
        ? 'mobile'
        : agent.is_tablet
          ? 'tablet'
          : 'desktop';

    const platform =
        typeof agent.platform === 'string' && agent.platform.length > 0
            ? agent.platform
            : null;

    return {
        ip: typeof meta.ip_address === 'string' ? meta.ip_address : null,
        platform,
        device: platform ? device : null,
    };
};

const getDeviceIcon = (device: NotificationMeta['device']) => {
    if (!device) {
        return null;
    }

    if (device === 'mobile') {
        return Smartphone;
    }

    if (device === 'tablet') {
        return Tablet;
    }

    return Laptop;
};

// Manual Load More Logic
const allNotifications = ref<Notification[]>([...props.notifications.data]);
const loadingMore = ref(false);

const loadMore = () => {
    const nextUrl =
        (props.notifications.links?.next as string) ||
        props.notifications.next_page_url;
    if (!nextUrl || loadingMore.value) return;

    loadingMore.value = true;

    router.visit(nextUrl, {
        method: 'get',
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        only: ['notifications'],
        onSuccess: () => {
            if (props.notifications.data.length > 0) {
                // Kinetic (Inertia::scroll) merges data into props automatically.
                // We should sync our local state with the accumulated props instead of pushing duplicates.
                allNotifications.value = [...props.notifications.data];
            }
        },
        onFinish: () => {
            loadingMore.value = false;
        },
    });
};

const markAsRead = (notification: Notification) => {
    if (notification.readAt) return; // Already read

    const url = read.url(notification.id);

    // Optimistic update
    notification.readAt = new Date().toISOString();

    // Also update in allNotifications if it's a different reference
    const index = allNotifications.value.findIndex(
        (n) => n.id === notification.id,
    );
    if (index !== -1) {
        allNotifications.value[index].readAt = notification.readAt;
    }

    router.visit(url, {
        method: 'patch',
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // Revert changes on error
            notification.readAt = null;
            const index = allNotifications.value.findIndex(
                (n) => n.id === notification.id,
            );
            if (index !== -1) {
                allNotifications.value[index].readAt = null;
            }
        },
    });
};

const toggleReadStatus = (notification: Notification) => {
    const isRead = !!notification.readAt;
    const url = isRead
        ? unread.url(notification.id)
        : read.url(notification.id);

    // Optimistic update
    notification.readAt = isRead ? null : new Date().toISOString();

    // Also update in allNotifications if it's a different reference
    const index = allNotifications.value.findIndex(
        (n) => n.id === notification.id,
    );
    if (index !== -1) {
        allNotifications.value[index].readAt = notification.readAt;
    }

    router.visit(url, {
        method: 'patch',
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // Revert changes on error
            notification.readAt = isRead ? new Date().toISOString() : null;
            const index = allNotifications.value.findIndex(
                (n) => n.id === notification.id,
            );
            if (index !== -1) {
                allNotifications.value[index].readAt = notification.readAt;
            }
        },
    });
};

const archiveNotification = (notification: Notification) => {
    // Optimistic update: Remove from list (and mentally mark as read/archived)
    allNotifications.value = allNotifications.value.filter(
        (n) => n.id !== notification.id,
    );
    localCounts.value.unread = Math.max(
        0,
        localCounts.value.unread - (notification.readAt ? 0 : 1),
    );
    localCounts.value.archived++;
    localCounts.value.total--;

    // Use manual URL since generated route might not be ready yet
    // Or if we trust it eventually, but let's be safe with manual path for now
    // effectively: /notifications/{id}/archive
    router.visit(`/notifications/${notification.id}/archive`, {
        method: 'patch',
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // Revert ... complicated to revert list removal efficiently without index, but fetch restart is ok
            router.reload();
        },
    });
};

const markAllAsReadGlobal = () => {
    if (unreadCount.value === 0) return;

    // Optimistic update
    allNotifications.value.forEach((n) => {
        if (!n.readAt) {
            n.readAt = new Date().toISOString();
        }
    });

    // Reset counts
    localCounts.value.unread = 0;

    router.visit('/notifications/read-all', {
        method: 'patch',
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // Revert
            router.reload();
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Notifications')" />

        <NotificationsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('Notifications')"
                    :description="
                        $t('Stay updated with important events and alerts')
                    "
                />

                <!-- Actions bar -->
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">
                        {{ unreadCount }} {{ $t('unread') }}
                    </span>
                    <Button
                        v-if="unreadCount > 0"
                        variant="link"
                        size="sm"
                        class="h-auto px-0 py-0 text-sm text-muted-foreground hover:text-foreground"
                        @click="markAllAsReadGlobal"
                    >
                        {{ $t('Mark all as read') }}
                    </Button>
                </div>

                <!-- Notifications list (flat) -->
                <TooltipProvider
                    v-if="allNotifications.length > 0"
                    :delay-duration="10"
                >
                    <div class="space-y-1">
                        <!-- Item with click handler to mark as read -->
                        <Item
                            v-for="notification in allNotifications"
                            :key="notification.id"
                            size="sm"
                            class="group cursor-pointer transition-colors hover:bg-muted/50"
                            :class="{ 'bg-muted/100': !notification.readAt }"
                            @click="markAsRead(notification)"
                        >
                            <ItemMedia
                                variant="icon"
                                class="text-muted-foreground"
                            >
                                <component
                                    :is="getIcon(notification.icon)"
                                    class="size-5"
                                />
                            </ItemMedia>

                            <ItemContent>
                                <ItemTitle
                                    :class="{
                                        'font-semibold': !notification.readAt,
                                    }"
                                >
                                    {{ notification.title }}
                                </ItemTitle>
                                <ItemDescription class="mb-1 line-clamp-none">
                                    {{ notification.body }}
                                </ItemDescription>
                                <div
                                    class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground"
                                >
                                    <span class="flex items-center gap-1.5">
                                        <Clock class="size-3.5" />
                                        <span>{{
                                            formatDateTime(
                                                notification.createdAt,
                                            )
                                        }}</span>
                                    </span>

                                    <span
                                        v-if="
                                            getNotificationMeta(notification).ip
                                        "
                                        class="flex items-center gap-1.5"
                                    >
                                        <MapPin class="size-3.5" />
                                        <span>{{
                                            getNotificationMeta(notification).ip
                                        }}</span>
                                    </span>

                                    <span
                                        v-if="
                                            getNotificationMeta(notification)
                                                .platform
                                        "
                                        class="flex items-center gap-1.5"
                                    >
                                        <component
                                            v-if="
                                                getDeviceIcon(
                                                    getNotificationMeta(
                                                        notification,
                                                    ).device,
                                                )
                                            "
                                            :is="
                                                getDeviceIcon(
                                                    getNotificationMeta(
                                                        notification,
                                                    ).device,
                                                )
                                            "
                                            class="size-3.5"
                                        />
                                        <span>{{
                                            getNotificationMeta(notification)
                                                .platform
                                        }}</span>
                                    </span>
                                </div>
                            </ItemContent>

                            <ItemActions
                                class="flex items-center gap-1 text-xs text-muted-foreground"
                            >
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="size-8"
                                            @click.stop="
                                                toggleReadStatus(notification)
                                            "
                                        >
                                            <component
                                                :is="
                                                    notification.readAt
                                                        ? CheckCheck
                                                        : Check
                                                "
                                                class="size-4"
                                                :class="{
                                                    'text-primary':
                                                        notification.readAt,
                                                    'text-muted-foreground':
                                                        !notification.readAt,
                                                }"
                                            />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>
                                            {{
                                                notification.readAt
                                                    ? $t('Mark as unread')
                                                    : $t('Mark as read')
                                            }}
                                        </p>
                                    </TooltipContent>
                                </Tooltip>

                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="size-8"
                                            @click.stop="
                                                archiveNotification(
                                                    notification,
                                                )
                                            "
                                        >
                                            <Archive class="size-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{{ $t('Archive') }}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </ItemActions>
                        </Item>
                    </div>

                    <!-- Load More Button -->
                    <div
                        v-if="
                            props.notifications.links?.next ||
                            props.notifications.next_page_url
                        "
                        class="flex justify-center pt-4"
                    >
                        <Button
                            variant="outline"
                            :disabled="loadingMore"
                            @click="loadMore"
                        >
                            <Loader2
                                v-if="loadingMore"
                                class="mr-2 size-4 animate-spin"
                            />
                            {{
                                loadingMore ? $t('Loading...') : $t('Show more')
                            }}
                        </Button>
                    </div>
                </TooltipProvider>

                <!-- Empty state -->
                <Empty
                    v-else
                    class="mx-auto flex w-full flex-col items-center justify-center py-12 text-center"
                >
                    <EmptyHeader
                        class="flex flex-col items-center justify-center"
                    >
                        <EmptyMedia variant="icon" class="mx-auto">
                            <Bell />
                        </EmptyMedia>
                        <EmptyTitle class="text-center">{{
                            $t("You're all caught up")
                        }}</EmptyTitle>
                        <EmptyDescription class="text-center">{{
                            $t('No notifications to show')
                        }}</EmptyDescription>
                    </EmptyHeader>
                </Empty>
            </div>
        </NotificationsLayout>
    </AppLayout>
</template>
