<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
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
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { cn } from '@/lib/utils';
import { update as updateCountry } from '@/routes/country';
import { edit } from '@/routes/language-and-region';
import { switchMethod as switchLocale } from '@/routes/locale';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Ban, Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import CountryFlag from 'vue-country-flag-next';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

interface Language {
    value: string;
    label: string;
}

interface Currency {
    value: string;
    label: string;
    name: string;
}

interface Country {
    value: string;
    label: string;
}

interface Props {
    languages: Language[];
    countries: Country[];
    currencies: Currency[];
    currentLocale: string;
    currentCountry: string | null;
    currentCurrency: string | null;
    countryToCurrency: Record<string, string[]>;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: $t('Language & Region'),
        href: edit().url,
    },
];

// Language form
const languageForm = useForm({
    locale: props.currentLocale,
});

const submitLanguage = () => {
    languageForm.post(switchLocale().url, {
        preserveScroll: true,
    });
};

// Region form
const countryForm = useForm({
    country: props.currentCountry || '',
    currency: props.currentCurrency || '',
});

const openCountry = ref(false);

const submitCountry = () => {
    countryForm.patch(updateCountry().url, {
        preserveScroll: true,
    });
};

const availableCurrencies = computed(() => {
    if (!countryForm.country) {
        return props.currencies;
    }

    const validCurrencyCodes = props.countryToCurrency[countryForm.country];

    if (validCurrencyCodes && validCurrencyCodes.length > 0) {
        return props.currencies.filter((c) =>
            validCurrencyCodes.includes(c.value),
        );
    }

    return props.currencies;
});

const selectedCurrency = computed(() => {
    if (!countryForm.currency) {
        return null;
    }
    return props.currencies.find((c) => c.value === countryForm.currency);
});

const selectedCountry = computed(() => {
    if (!countryForm.country) {
        return null;
    }
    return props.countries.find((c) => c.value === countryForm.country);
});

// Watch for country changes to auto-select currency if possible
watch(
    () => countryForm.country,
    (newCountry) => {
        if (newCountry) {
            const suggestedCurrencies = props.countryToCurrency[newCountry];
            if (suggestedCurrencies && suggestedCurrencies.length > 0) {
                // If current currency is not in the new valid list, switch to the first valid one
                if (!suggestedCurrencies.includes(countryForm.currency)) {
                    countryForm.currency = suggestedCurrencies[0];
                }
            } else {
                countryForm.currency = '';
            }
        } else {
            countryForm.currency = '';
        }
    },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Language & Region')" />

        <SettingsLayout>
            <div class="space-y-8">
                <!-- Interface Language Form -->
                <div>
                    <HeadingSmall
                        :title="$t('Interface Language')"
                        :description="
                            $t(
                                'Choose your preferred language for the interface',
                            )
                        "
                    />

                    <form
                        @submit.prevent="submitLanguage"
                        class="mt-6 space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label>{{ $t('Language') }}</Label>
                            <Select v-model="languageForm.locale">
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        :placeholder="$t('Select a language')"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="language in languages"
                                            :key="language.value"
                                            :value="language.value"
                                        >
                                            {{ language.label }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    $t(
                                        'This will change the language of the interface.',
                                    )
                                }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button
                                type="submit"
                                :disabled="languageForm.processing"
                            >
                                {{ $t('Save language') }}
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="languageForm.recentlySuccessful"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ $t('Saved.') }}
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>

                <!-- Region Form -->
                <div>
                    <HeadingSmall
                        :title="$t('Region Settings')"
                        :description="
                            $t('Select your country and preferred currency')
                        "
                    />

                    <form
                        @submit.prevent="submitCountry"
                        class="mt-6 space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label>{{ $t('Country') }}</Label>
                            <Popover v-model:open="openCountry">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        :aria-expanded="openCountry"
                                        class="w-full justify-between font-normal"
                                        :class="
                                            !countryForm.country &&
                                            'text-muted-foreground'
                                        "
                                        type="button"
                                    >
                                        <div class="flex items-center">
                                            <template v-if="selectedCountry">
                                                <span
                                                    class="mr-2 flex items-center"
                                                    style="
                                                        align-items: flex-start;
                                                    "
                                                >
                                                    <CountryFlag
                                                        :country="
                                                            selectedCountry.value.toLowerCase()
                                                        "
                                                        rounded
                                                        size="normal"
                                                        style="
                                                            margin-top: -8px;
                                                            margin-left: -13px;
                                                        "
                                                    />
                                                </span>
                                                {{ selectedCountry.label }}
                                            </template>
                                            <template v-else>
                                                {{ $t('Select country...') }}
                                            </template>
                                        </div>
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
                                            :placeholder="
                                                $t('Search country...')
                                            "
                                        />
                                        <CommandEmpty>{{
                                            $t('No country found.')
                                        }}</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem
                                                    :value="
                                                        $t(
                                                            'Country not selected',
                                                        )
                                                    "
                                                    @select="
                                                        () => {
                                                            countryForm.country =
                                                                '';
                                                            openCountry = false;
                                                        }
                                                    "
                                                >
                                                    <Ban class="mr-2 h-4 w-4" />
                                                    {{
                                                        $t(
                                                            'Country not selected',
                                                        )
                                                    }}
                                                    <Check
                                                        :class="
                                                            cn(
                                                                'ml-auto h-4 w-4',
                                                                !countryForm.country
                                                                    ? 'opacity-100'
                                                                    : 'opacity-0',
                                                            )
                                                        "
                                                    />
                                                </CommandItem>
                                                <CommandItem
                                                    v-for="country in countries"
                                                    :key="country.value"
                                                    :value="country.label"
                                                    @select="
                                                        () => {
                                                            countryForm.country =
                                                                country.value;
                                                            openCountry = false;
                                                        }
                                                    "
                                                >
                                                    <span
                                                        class="mr-2 flex items-center"
                                                    >
                                                        <CountryFlag
                                                            :country="
                                                                country.value.toLowerCase()
                                                            "
                                                            rounded
                                                            size="normal"
                                                            style="
                                                                margin-top: -8px;
                                                                margin-left: -13px;
                                                            "
                                                        />
                                                    </span>
                                                    {{ country.label }}
                                                    <Check
                                                        :class="
                                                            cn(
                                                                'ml-auto h-4 w-4',
                                                                countryForm.country ===
                                                                    country.value
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
                                        'This helps us provide region-specific content and settings.',
                                    )
                                }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label>{{ $t('Currency') }}</Label>
                            <Select v-model="countryForm.currency">
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        :placeholder="$t('Select currency...')"
                                    >
                                        <template v-if="selectedCurrency">
                                            <div
                                                class="flex items-center gap-1"
                                            >
                                                <Badge
                                                    variant="default"
                                                    class="mr-2 rounded-sm px-1"
                                                >
                                                    {{ selectedCurrency.value }}
                                                </Badge>
                                                {{ selectedCurrency.name }}
                                            </div>
                                        </template>
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="null_value">
                                            <div
                                                class="flex items-center gap-2 text-muted-foreground"
                                            >
                                                {{
                                                    $t('Currency not selected')
                                                }}
                                            </div>
                                        </SelectItem>
                                        <SelectItem
                                            v-for="currency in availableCurrencies"
                                            :key="currency.value"
                                            :value="currency.value"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <Badge
                                                    variant="default"
                                                    class="mr-2 rounded-sm px-1"
                                                >
                                                    {{ currency.value }}
                                                </Badge>
                                                {{ currency.name }}
                                            </div>
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button
                                type="submit"
                                :disabled="countryForm.processing"
                            >
                                {{ $t('Save region settings') }}
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="countryForm.recentlySuccessful"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ $t('Saved.') }}
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
