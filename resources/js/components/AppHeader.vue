<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { convertNavigationItem } from '@/composables/useNavigation';
import { toUrl, urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes';
import type {
    BreadcrumbItem,
    NavItem,
    Navigation,
    NavigationItem,
    NavigationSection,
    UIConfigLayout,
    UIConfigToggles,
} from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Menu, Search } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    width?: 'container' | 'full';
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    width: 'container',
});

const page = usePage();
const auth = computed(() => page.props.auth);
const navigation = computed(
    () => page.props.navigation as Navigation | undefined,
);
const toggles = computed(() => usePage().props.toggles as UIConfigToggles);
const layout = computed(() => usePage().props.layout as UIConfigLayout);

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'text-foreground dark:bg-accent dark:text-accent-foreground'
            : '',
);

const mainNavItems = computed<NavItem[]>(() => {
    if (navigation.value?.sidebar) {
        return navigation.value.sidebar.flatMap((section: NavigationSection) =>
            section.items
                .filter((item: NavigationItem) => !item.isSeparator)
                .map(convertNavigationItem),
        );
    }

    return [
        {
            title: $t('Dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];
});

const rightNavItems: NavItem[] = [
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
];
</script>

<template>
    <div>
        <div class="relative z-50 border-b border-sidebar-border/80">
            <div
                class="mx-auto flex h-16 items-center px-4"
                :class="props.width === 'full' ? 'max-w-full' : 'md:max-w-7xl'"
            >
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only"
                                >Navigation Menu</SheetTitle
                            >
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon
                                    class="size-6 fill-current text-foreground"
                                />
                            </SheetHeader>
                            <div
                                class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
                            >
                                <nav class="-mx-3 space-y-1">
                                    <template
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                    >
                                        <div
                                            v-if="
                                                item.children &&
                                                item.children.length > 0
                                            "
                                        >
                                            <div
                                                class="px-3 py-2 text-xs font-semibold text-muted-foreground"
                                            >
                                                {{ item.title }}
                                            </div>
                                            <Link
                                                v-for="child in item.children"
                                                :key="child.title"
                                                :href="child.href"
                                                class="flex items-center gap-x-3 rounded-lg px-3 py-2 pl-6 text-sm font-medium hover:bg-accent"
                                                :class="
                                                    activeItemStyles(child.href)
                                                "
                                            >
                                                <component
                                                    v-if="child.icon"
                                                    :is="child.icon"
                                                    class="h-4 w-4"
                                                />
                                                {{ child.title }}
                                                <span
                                                    v-if="child.badge"
                                                    class="ml-auto inline-flex items-center rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground"
                                                >
                                                    {{ child.badge }}
                                                </span>
                                            </Link>
                                        </div>

                                        <Link
                                            v-else
                                            :href="item.href"
                                            class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                            :class="activeItemStyles(item.href)"
                                        >
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="h-5 w-5"
                                            />
                                            {{ item.title }}
                                            <span
                                                v-if="item.badge"
                                                class="ml-auto inline-flex items-center rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground"
                                            >
                                                {{ item.badge }}
                                            </span>
                                        </Link>
                                    </template>
                                </nav>
                                <div class="flex flex-col space-y-4">
                                    <a
                                        v-for="item in rightNavItems"
                                        :key="item.title"
                                        :href="toUrl(item.href)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center space-x-2 text-sm font-medium"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="h-5 w-5"
                                        />
                                        <span>{{ item.title }}</span>
                                    </a>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link
                    :href="dashboard()"
                    class="relative flex items-center gap-x-2 overflow-visible"
                >
                    <div
                        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
                    >
                        <AppLogoIcon
                            class="size-5 fill-current text-sidebar-primary-foreground"
                        />
                    </div>
                    <div class="grid flex-1 text-left text-sm leading-tight">
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

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10">
                        <NavigationMenuList class="space-x-2">
                            <NavigationMenuItem
                                v-for="(item, index) in mainNavItems"
                                :key="index"
                                class="relative"
                            >
                                <template
                                    v-if="
                                        item.children &&
                                        item.children.length > 0
                                    "
                                >
                                    <NavigationMenuTrigger
                                        :class="[
                                            'h-9 px-3',
                                            activeItemStyles(item.href),
                                        ]"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="mr-2 h-4 w-4"
                                        />
                                        {{ item.title }}
                                    </NavigationMenuTrigger>
                                    <NavigationMenuContent
                                        :class="
                                            cn(
                                                'rounded-lg border bg-popover p-2 opacity-100 shadow-md',
                                            )
                                        "
                                    >
                                        <ul
                                            class="grid gap-2 md:w-[400px] lg:w-[500px] lg:grid-cols-[.75fr_1fr]"
                                        >
                                            <li class="row-span-12">
                                                <NavigationMenuLink as-child>
                                                    <Link
                                                        :href="item.href"
                                                        class="flex h-full w-full flex-col justify-end rounded-lg bg-gradient-to-b from-muted/50 to-muted p-6 no-underline outline-none select-none focus:shadow-md"
                                                    >
                                                        <div
                                                            class="mt-4 mb-2 text-lg font-medium"
                                                        >
                                                            {{ item.title }}
                                                        </div>
                                                        <p
                                                            v-if="
                                                                item.description
                                                            "
                                                            class="text-sm leading-tight text-muted-foreground"
                                                        >
                                                            {{
                                                                item.description
                                                            }}
                                                        </p>
                                                        <p
                                                            v-else
                                                            class="text-sm leading-tight text-muted-foreground"
                                                        >
                                                            {{
                                                                $t(
                                                                    'View all options and settings.',
                                                                )
                                                            }}
                                                        </p>
                                                    </Link>
                                                </NavigationMenuLink>
                                            </li>
                                            <li
                                                v-for="child in item.children"
                                                :key="child.title"
                                            >
                                                <div
                                                    v-if="
                                                        child.children &&
                                                        child.children.length >
                                                            0
                                                    "
                                                    class="flex flex-col gap-2 p-2"
                                                >
                                                    <div
                                                        class="flex items-center text-sm font-medium text-foreground"
                                                    >
                                                        <component
                                                            v-if="child.icon"
                                                            :is="child.icon"
                                                            class="mr-2 h-4 w-4"
                                                        />
                                                        {{ child.title }}
                                                    </div>
                                                    <ul
                                                        class="flex flex-col gap-1 border-l pl-2"
                                                    >
                                                        <li
                                                            v-for="subChild in child.children"
                                                            :key="
                                                                subChild.title
                                                            "
                                                        >
                                                            <NavigationMenuLink
                                                                as-child
                                                            >
                                                                <a
                                                                    :href="
                                                                        toUrl(
                                                                            subChild.href,
                                                                        )
                                                                    "
                                                                    class="block rounded-md p-2 text-sm leading-none no-underline transition-colors outline-none select-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                                                >
                                                                    <div
                                                                        class="flex items-center"
                                                                    >
                                                                        <component
                                                                            v-if="
                                                                                subChild.icon
                                                                            "
                                                                            :is="
                                                                                subChild.icon
                                                                            "
                                                                            class="mr-2 h-4 w-4"
                                                                        />
                                                                        {{
                                                                            subChild.title
                                                                        }}
                                                                        <span
                                                                            v-if="
                                                                                subChild.badge
                                                                            "
                                                                            class="ml-auto inline-flex items-center rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-medium text-primary-foreground"
                                                                        >
                                                                            {{
                                                                                subChild.badge
                                                                            }}
                                                                        </span>
                                                                    </div>
                                                                    <p
                                                                        v-if="
                                                                            subChild.description
                                                                        "
                                                                        class="mt-1 line-clamp-1 pl-6 text-xs text-muted-foreground"
                                                                    >
                                                                        {{
                                                                            subChild.description
                                                                        }}
                                                                    </p>
                                                                </a>
                                                            </NavigationMenuLink>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <NavigationMenuLink
                                                    v-else
                                                    as-child
                                                >
                                                    <a
                                                        :href="
                                                            toUrl(child.href)
                                                        "
                                                        class="block space-y-1 rounded-lg p-3 leading-none no-underline transition-colors outline-none select-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                                    >
                                                        <div
                                                            class="flex items-center text-sm leading-none font-medium"
                                                        >
                                                            <component
                                                                v-if="
                                                                    child.icon
                                                                "
                                                                :is="child.icon"
                                                                class="mr-2 h-4 w-4"
                                                            />
                                                            {{ child.title }}
                                                            <span
                                                                v-if="
                                                                    child.badge
                                                                "
                                                                class="ml-auto inline-flex items-center rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground"
                                                            >
                                                                {{
                                                                    child.badge
                                                                }}
                                                            </span>
                                                        </div>
                                                        <p
                                                            v-if="
                                                                child.description
                                                            "
                                                            class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                                                        >
                                                            {{
                                                                child.description
                                                            }}
                                                        </p>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                        </ul>
                                    </NavigationMenuContent>
                                </template>

                                <template v-else>
                                    <NavigationMenuLink
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                            'h-9 cursor-pointer px-3',
                                        ]"
                                        :href="item.href"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="mr-2 h-4 w-4"
                                        />
                                        {{ item.title }}
                                    </NavigationMenuLink>
                                    <div
                                        v-if="isCurrentRoute(item.href)"
                                        class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-primary"
                                    ></div>
                                </template>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer"
                        >
                            <Search
                                class="size-5 opacity-80 group-hover:opacity-100"
                            />
                        </Button>

                        <div class="hidden space-x-1 lg:flex">
                            <template
                                v-for="item in rightNavItems"
                                :key="item.title"
                            >
                                <TooltipProvider :delay-duration="0">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                                class="group h-9 w-9 cursor-pointer"
                                            >
                                                <a
                                                    :href="toUrl(item.href)"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <span class="sr-only">{{
                                                        item.title
                                                    }}</span>
                                                    <component
                                                        :is="item.icon"
                                                        class="size-5 opacity-80 group-hover:opacity-100"
                                                    />
                                                </a>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ item.title }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </template>
                        </div>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar
                                    class="size-8 overflow-hidden rounded-full"
                                >
                                    <AvatarImage
                                        v-if="auth.user.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user.name"
                                    />
                                    <AvatarFallback
                                        class="rounded-lg bg-muted font-semibold text-foreground"
                                    >
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-muted-foreground"
                :class="props.width === 'full' ? 'max-w-full' : 'md:max-w-7xl'"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
