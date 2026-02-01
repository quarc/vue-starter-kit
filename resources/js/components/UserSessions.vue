<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { MapPin, Monitor, Smartphone, Tablet, Trash2 } from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Item,
    ItemActions,
    ItemContent,
    ItemDescription,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import { Label } from '@/components/ui/label';

interface Session {
    id: string;
    ip_address: string;
    is_current_device: boolean;
    last_active: string;
    last_active_at: string;
    agent: {
        is_desktop: boolean;
        is_mobile: boolean;
        is_tablet: boolean;
        platform: string;
        browser: string;
        browser_version: string;
    };
}

interface Props {
    sessions: Session[];
}

const props = defineProps<Props>();

const deletePasswordInput = ref<HTMLInputElement | null>(null);
const logoutPasswordInput = ref<HTMLInputElement | null>(null);
const deleteSessionId = ref<string | null>(null);
const showLogoutOthersDialog = ref(false);

const deleteForm = useForm({
    password: '',
});

const logoutOthersForm = useForm({
    password: '',
});

const deleteSession = (sessionId: string) => {
    deleteForm.delete(`/settings/sessions/${sessionId}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteSessionId.value = null;
            deleteForm.reset();
        },
        onError: () => {
            nextTick(() => {
                deletePasswordInput.value?.focus();
            });
        },
    });
};

const logoutOtherSessions = () => {
    logoutOthersForm.delete('/settings/sessions', {
        preserveScroll: true,
        onSuccess: () => {
            showLogoutOthersDialog.value = false;
            logoutOthersForm.reset();
        },
        onError: () => {
            nextTick(() => {
                logoutPasswordInput.value?.focus();
            });
        },
    });
};

const getDeviceIcon = (agent: Session['agent']) => {
    if (agent.is_mobile) return Smartphone;
    if (agent.is_tablet) return Tablet;
    return Monitor;
};

const getSessionTitle = (session: Session) => {
    const platform = session.agent.platform || $t('Unknown device');
    const browser = session.agent.browser || $t('Unknown browser');
    const version = session.agent.browser_version
        ? ` ${session.agent.browser_version}`
        : '';

    return `${platform} — ${browser}${version}`;
};

const otherSessions = computed(() =>
    props.sessions.filter((s) => !s.is_current_device),
);

// Use the translation function
import { useI18n } from 'vue-i18n';
const { t: $t } = useI18n();
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            :title="$t('Active sessions')"
            :description="
                $t(
                    'Manage and log out your active sessions on other browsers and devices.',
                )
            "
        />

        <div class="space-y-4">
            <!-- Sessions list -->
            <div class="space-y-2">
                <Item
                    v-for="session in sessions"
                    :key="session.id"
                    variant="outline"
                    size="sm"
                >
                    <ItemMedia variant="icon" class="rounded-sm">
                        <component
                            :is="getDeviceIcon(session.agent)"
                            class="h-4 w-4"
                        />
                    </ItemMedia>

                    <ItemContent>
                        <ItemTitle>
                            {{ getSessionTitle(session) }}
                        </ItemTitle>
                        <ItemDescription class="flex items-center gap-2">
                            <div class="flex items-center">
                                <MapPin class="mr-1 h-4 w-4" />
                                {{ session.ip_address }}
                            </div>
                            <div
                                v-if="session.is_current_device"
                                class="flex items-center"
                            >
                                {{ $t('Current session') }}
                            </div>
                            <div v-else class="flex items-center">
                                {{ session.last_active }}
                            </div>
                        </ItemDescription>
                    </ItemContent>

                    <!-- Delete button (only for other sessions) -->
                    <ItemActions v-if="!session.is_current_device">
                        <Dialog
                            :open="deleteSessionId === session.id"
                            @update:open="
                                (open) => {
                                    if (!open) {
                                        deleteSessionId = null;
                                        deleteForm.reset();
                                        deleteForm.clearErrors();
                                    }
                                }
                            "
                        >
                            <DialogTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-8 w-8 text-muted-foreground hover:text-destructive"
                                    @click="deleteSessionId = session.id"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    <span class="sr-only">{{
                                        $t('Log out session')
                                    }}</span>
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <form
                                    @submit.prevent="deleteSession(session.id)"
                                    class="space-y-6"
                                >
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle>{{
                                            $t('Log out session')
                                        }}</DialogTitle>
                                        <DialogDescription>
                                            {{
                                                $t(
                                                    'Are you sure you want to log out this session? You may need to log in again on that device.',
                                                )
                                            }}
                                        </DialogDescription>
                                    </DialogHeader>

                                    <div class="grid gap-2">
                                        <Label for="password" class="sr-only">{{
                                            $t('Password')
                                        }}</Label>
                                        <input
                                            id="password"
                                            type="password"
                                            v-model="deleteForm.password"
                                            ref="deletePasswordInput"
                                            :placeholder="$t('Password')"
                                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                        />
                                        <InputError
                                            :message="
                                                deleteForm.errors.password
                                            "
                                        />
                                    </div>

                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button
                                                variant="secondary"
                                                type="button"
                                                @click="
                                                    deleteForm.reset();
                                                    deleteForm.clearErrors();
                                                "
                                            >
                                                {{ $t('Cancel') }}
                                            </Button>
                                        </DialogClose>

                                        <Button
                                            type="submit"
                                            variant="destructive"
                                            :disabled="deleteForm.processing"
                                        >
                                            {{ $t('Log out session') }}
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </ItemActions>
                </Item>
            </div>

            <!-- Logout all other sessions button -->
            <div v-if="otherSessions.length > 0" class="pt-2">
                <Dialog v-model:open="showLogoutOthersDialog">
                    <DialogTrigger as-child>
                        <Button variant="default">
                            {{ $t('Log out other sessions') }}
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <form
                            @submit.prevent="logoutOtherSessions"
                            class="space-y-6"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle>{{
                                    $t('Log out other browser sessions')
                                }}</DialogTitle>
                                <DialogDescription>
                                    {{
                                        $t(
                                            'Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.',
                                        )
                                    }}
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="logout-password" class="sr-only">{{
                                    $t('Password')
                                }}</Label>
                                <input
                                    id="logout-password"
                                    type="password"
                                    v-model="logoutOthersForm.password"
                                    ref="logoutPasswordInput"
                                    :placeholder="$t('Password')"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <InputError
                                    :message="logoutOthersForm.errors.password"
                                />
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button
                                        variant="secondary"
                                        type="button"
                                        @click="
                                            logoutOthersForm.reset();
                                            logoutOthersForm.clearErrors();
                                        "
                                    >
                                        {{ $t('Cancel') }}
                                    </Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    variant="destructive"
                                    :disabled="logoutOthersForm.processing"
                                >
                                    {{ $t('Log out other sessions') }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Empty state -->
            <div
                v-if="sessions.length === 0"
                class="rounded-lg border border-dashed border-border/50 bg-muted/30 p-8 text-center"
            >
                <Monitor class="mx-auto h-10 w-10 text-muted-foreground/50" />
                <p class="mt-3 text-sm text-muted-foreground">
                    {{ $t('No active sessions found') }}
                </p>
            </div>
        </div>
    </div>
</template>
