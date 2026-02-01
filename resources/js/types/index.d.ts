import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

// ============================================
// User & Auth
// ============================================

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    timezone?: string;
    date_format?: string;
    time_format?: string;
    unread_notifications_count?: number;
}

export interface Auth {
    user: User;
}

export interface Session {
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

export interface Notification {
    id: string;
    icon: string;
    title: string;
    body: string;
    level?: string;
    readAt: string | null;
    archivedAt: string | null;
    createdAt: string;
    meta?: Record<string, unknown>;
}

// ============================================
// Navigation (Backend -> Frontend)
// ============================================

export interface NavigationItem {
    name: string;
    label: string;
    icon: string | null;
    route: string | null;
    children?: NavigationItem[];
    badge?: string | number;
    isSeparator?: boolean;
    description?: string;
}

export interface NavigationSection {
    label: string;
    items: NavigationItem[];
}

export interface Navigation {
    sidebar: NavigationSection[];
}

// ============================================
// Navigation (Frontend processed)
// ============================================

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    badge?: string | number;
    children?: NavItem[];
    isActive?: boolean;
    isSeparator?: boolean;
    description?: string;
}

// ============================================
// Breadcrumbs
// ============================================

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

// ============================================
// UI Configuration
// ============================================

export interface UIConfigLayout {
    navigation: 'sidebar' | 'topbar';
    app_width: 'container' | 'full';
    title: string;
    slogan: string;
    settings_navigation: 'vertical' | 'horizontal';
}

export interface UIConfigBrand {
    show_icon: boolean;
    show_name: boolean;
}

export interface UIConfigSidebar {
    variant: 'sidebar' | 'floating' | 'inset';
}

export interface UIConfigToggles {
    show_slogan: boolean;
}

// ============================================
// Page Props
// ============================================

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    timezone: string;
    auth: Auth;
    sidebarOpen: boolean;
    navigation?: Navigation;
    layout: UIConfigLayout;
    brand: UIConfigBrand;
    sidebar: UIConfigSidebar;
    toggles: UIConfigToggles;
};
