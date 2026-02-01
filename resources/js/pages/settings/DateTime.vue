<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { getDateFormats, getTimeFormats } from '@/lib/formats';
import { getTimezones, type Timezone } from '@/lib/timezones';
import { cn } from '@/lib/utils';
import { edit, update } from '@/routes/date-time';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t, locale } = useI18n();

interface Props {
    currentTimezone: string | null;
    currentDateFormat: string | null;
    currentTimeFormat: string | null;
}

const props = defineProps<Props>();

// Generate timezones and formats locally
const timezones = computed(() => getTimezones());
const dateFormats = computed(() => getDateFormats(locale.value as string));
const timeFormats = computed(() => getTimeFormats(locale.value as string));

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: $t('Date & Time'),
        href: edit().url,
    },
];

// Group timezones by region
const groupedTimezones = computed(() => {
    const groups: { [key: string]: Timezone[] } = {};
    timezones.value.forEach((tz) => {
        if (!groups[tz.region]) {
            groups[tz.region] = [];
        }
        groups[tz.region].push(tz);
    });
    return groups;
});

const openTimezone = ref(false);

const form = useForm({
    timezone: props.currentTimezone,
    date_format: props.currentDateFormat,
    time_format: props.currentTimeFormat,
});

const selectedTimezone = computed(() => {
    return timezones.value.find((tz) => tz.value === form.timezone);
});

const selectedDateFormat = computed(() => {
    return dateFormats.value.find((f) => f.value === form.date_format);
});

const selectedTimeFormat = computed(() => {
    return timeFormats.value.find((f) => f.value === form.time_format);
});

const submit = () => {
    form.patch(update().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Date & Time')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('Date & Time')"
                    :description="$t('Manage your date and time preferences')"
                />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label>{{ $t('Timezone') }}</Label>
                        <Popover v-model:open="openTimezone">
                            <PopoverTrigger as-child>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    :aria-expanded="openTimezone"
                                    class="w-full justify-between font-normal"
                                    :class="
                                        !form.timezone &&
                                        'text-muted-foreground'
                                    "
                                    type="button"
                                >
                                    {{
                                        selectedTimezone?.label ||
                                        $t('Select timezone...')
                                    }}
                                    <ChevronsUpDown
                                        class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                    />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent
                                class="w-[--radix-popover-trigger-width] p-0"
                            >
                                <Command>
                                    <CommandInput
                                        class="h-9"
                                        :placeholder="$t('Search timezone...')"
                                    />
                                    <CommandEmpty>{{
                                        $t('No timezone found.')
                                    }}</CommandEmpty>
                                    <CommandList>
                                        <CommandGroup>
                                            <CommandItem
                                                :value="
                                                    $t('Timezone not selected')
                                                "
                                                @select="
                                                    () => {
                                                        form.timezone = null;
                                                        openTimezone = false;
                                                    }
                                                "
                                            >
                                                {{
                                                    $t('Timezone not selected')
                                                }}
                                                <Check
                                                    :class="
                                                        cn(
                                                            'ml-auto h-4 w-4',
                                                            !form.timezone
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )
                                                    "
                                                />
                                            </CommandItem>
                                        </CommandGroup>
                                        <CommandGroup
                                            v-for="(
                                                tzList, region
                                            ) in groupedTimezones"
                                            :key="String(region)"
                                            :heading="String(region)"
                                        >
                                            <CommandItem
                                                v-for="timezone in tzList"
                                                :key="timezone.value"
                                                :value="timezone.label"
                                                @select="
                                                    () => {
                                                        form.timezone =
                                                            timezone.value;
                                                        openTimezone = false;
                                                    }
                                                "
                                            >
                                                {{ timezone.label }}
                                                <Check
                                                    :class="
                                                        cn(
                                                            'ml-auto h-4 w-4',
                                                            form.timezone ===
                                                                timezone.value
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )
                                                    "
                                                />
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        <p class="text-sm text-muted-foreground">
                            {{
                                $t(
                                    'Your timezone is used to display dates and times in your local time.',
                                )
                            }}
                        </p>
                    </div>

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label>{{ $t('Date format') }}</Label>
                            <Select v-model="form.date_format">
                                <SelectTrigger class="w-full">
                                    <span
                                        :class="
                                            selectedDateFormat
                                                ? ''
                                                : 'text-muted-foreground'
                                        "
                                    >
                                        {{
                                            selectedDateFormat?.example ||
                                            $t('Select format')
                                        }}
                                    </span>
                                    <SelectValue
                                        :value="form.date_format"
                                        class="sr-only"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">
                                        {{ $t('Not selected') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="format in dateFormats"
                                        :key="format.value"
                                        :value="format.value"
                                    >
                                        {{ format.example }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    $t(
                                        'This format is used to display dates throughout the system.',
                                    )
                                }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label>{{ $t('Time format') }}</Label>
                            <Select v-model="form.time_format">
                                <SelectTrigger class="w-full">
                                    <span
                                        :class="
                                            selectedTimeFormat
                                                ? ''
                                                : 'text-muted-foreground'
                                        "
                                    >
                                        {{
                                            selectedTimeFormat?.example ||
                                            $t('Select format')
                                        }}
                                    </span>
                                    <SelectValue
                                        :value="form.time_format"
                                        class="sr-only"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">
                                        {{ $t('Not selected') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="format in timeFormats"
                                        :key="format.value"
                                        :value="format.value"
                                    >
                                        {{ format.example }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    $t(
                                        'This format is used to display times throughout the system.',
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">{{
                            $t('Save changes')
                        }}</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-if="form.recentlySuccessful"
                                class="text-sm text-muted-foreground"
                            >
                                {{ $t('Saved.') }}
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
