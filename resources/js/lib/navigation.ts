import type { LucideIcon } from 'lucide-vue-next';
import * as LucideIcons from 'lucide-vue-next';

/**
 * Get the icon from lucide-vue-next by name
 */
export function getIcon(
    iconName: string | null | undefined,
): LucideIcon | undefined {
    if (!iconName) {
        return undefined;
    }

    // Convert the name to PascalCase (e.g. 'home' -> 'Home')
    const iconKey = iconName
        .split(/[-_\s]/)
        .map(
            (word) =>
                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase(),
        )
        .join('') as keyof typeof LucideIcons;

    const Icon = LucideIcons[iconKey] as LucideIcon | undefined;

    return Icon;
}

/**
 * Get the route URL
 */
export function getRouteUrl(route: string | null | undefined): string {
    if (!route) {
        return '#';
    }

    return route;
}
