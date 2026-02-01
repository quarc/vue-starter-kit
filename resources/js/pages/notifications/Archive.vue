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
import { archive } from '@/routes/notifications';
import type { Notification } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import * as LucideIcons from 'lucide-vue-next';
import {
    Archive,
    ArchiveRestore,
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
const breadcrumbItems = [{ title: $t('Archive'), href: archive().url }];

// Manual Load More Logic
const allNotifications = ref<Notification[]>([
    ...(props.notifications?.data ?? []),
]);
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
                // Kinetic merges data into props automatically.
                allNotifications.value = [...props.notifications.data];
            }
        },
        onFinish: () => {
            loadingMore.value = false;
        },
    });
};

const unarchiveNotification = (notification: Notification) => {
    // Optimistic update: Remove from list
    allNotifications.value = allNotifications.value.filter(
        (n) => n.id !== notification.id,
    );
    localCounts.value.archived--;
    localCounts.value.unread++; // It becomes unread
    localCounts.value.total++;

    // Use manual URL since generated route might not be ready yet
    // effectively: /notifications/{id}/unarchive
    router.visit(`/notifications/${notification.id}/unarchive`, {
        method: 'patch',
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            router.reload();
        },
    });
};

// Date Helpers
const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);

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
    return icon || LucideIcons.Archive;
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Archive')" />

        <NotificationsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('Archive')"
                    :description="$t('Archived notifications are stored here')"
                />

                <!-- Actions bar -->
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">
                        {{ localCounts.archived }} {{ $t('archived') }}
                    </span>
                </div>

                <!-- Archived notifications list (flat) -->
                <TooltipProvider
                    v-if="allNotifications.length > 0"
                    :delay-duration="0"
                >
                    <div class="space-y-1">
                        <Item
                            v-for="notification in allNotifications"
                            :key="notification.id"
                            size="sm"
                            class="group"
                            :class="{ 'bg-muted/100': !notification.readAt }"
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
                                                notification.archivedAt!,
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
                                                unarchiveNotification(
                                                    notification,
                                                )
                                            "
                                        >
                                            <ArchiveRestore class="size-4" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{{ $t('Restore') }}</p>
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
                            <Archive />
                        </EmptyMedia>
                        <EmptyTitle class="text-center">{{
                            $t('No archived notifications')
                        }}</EmptyTitle>
                        <EmptyDescription class="text-center">{{
                            $t('Archived notifications will appear here')
                        }}</EmptyDescription>
                    </EmptyHeader>
                </Empty>
            </div>
        </NotificationsLayout>
    </AppLayout>
</template>
