<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    InputGroup,
    InputGroupAddon,
    InputGroupButton,
    InputGroupInput,
} from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: $t('Password settings'),
        href: edit().url,
    },
];

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Password settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('Update password')"
                    :description="
                        $t(
                            'Ensure your account is using a long, random password to stay secure',
                        )
                    "
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password">{{
                            $t('Current password')
                        }}</Label>
                        <InputGroup class="mt-1">
                            <InputGroupInput
                                id="current_password"
                                name="current_password"
                                class="!shadow-none"
                                :type="
                                    showCurrentPassword ? 'text' : 'password'
                                "
                                autocomplete="current-password"
                                :placeholder="$t('Current password')"
                            />
                            <InputGroupAddon align="inline-end">
                                <InputGroupButton
                                    size="icon-sm"
                                    class="ml-1.5"
                                    type="button"
                                    @click="
                                        showCurrentPassword =
                                            !showCurrentPassword
                                    "
                                >
                                    <Eye v-if="!showCurrentPassword" />
                                    <EyeOff v-else />
                                </InputGroupButton>
                            </InputGroupAddon>
                        </InputGroup>
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">{{ $t('New password') }}</Label>
                        <InputGroup class="mt-1">
                            <InputGroupInput
                                id="password"
                                name="password"
                                class="!shadow-none"
                                :type="showNewPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                :placeholder="$t('New password')"
                            />
                            <InputGroupAddon
                                align="inline-end"
                                class="shadow-none"
                            >
                                <InputGroupButton
                                    size="icon-sm"
                                    class="ml-1.5"
                                    type="button"
                                    @click="showNewPassword = !showNewPassword"
                                >
                                    <Eye
                                        v-if="!showNewPassword"
                                        class="size-4"
                                    />
                                    <EyeOff v-else class="size-4" />
                                </InputGroupButton>
                            </InputGroupAddon>
                        </InputGroup>
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">{{
                            $t('Confirm password')
                        }}</Label>
                        <InputGroup class="mt-1">
                            <InputGroupInput
                                id="password_confirmation"
                                name="password_confirmation"
                                class="!shadow-none"
                                :type="
                                    showConfirmPassword ? 'text' : 'password'
                                "
                                autocomplete="new-password"
                                :placeholder="$t('Confirm password')"
                            />
                            <InputGroupAddon align="inline-end">
                                <InputGroupButton
                                    size="icon-sm"
                                    class="ml-1.5"
                                    type="button"
                                    @click="
                                        showConfirmPassword =
                                            !showConfirmPassword
                                    "
                                >
                                    <Eye
                                        v-if="!showConfirmPassword"
                                        class="size-4"
                                    />
                                    <EyeOff v-else class="size-4" />
                                </InputGroupButton>
                            </InputGroupAddon>
                        </InputGroup>
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-password-button"
                        >
                            {{ $t('Save password') }}</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                {{ $t('Saved.') }}
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
