import { getIcon, getRouteUrl } from '@/lib/navigation';
import type { NavItem, NavigationItem } from '@/types';

/**
 * Convert NavigationItem from backend to NavItem for frontend
 */
export function convertNavigationItem(item: NavigationItem): NavItem {
    const href = getRouteUrl(item.route);
    const icon = getIcon(item.icon);

    return {
        title: item.label,
        href: href as NonNullable<NavItem['href']>,
        icon,
        badge: item.badge,
        isSeparator: item.isSeparator,
        description: item.description,
        children: item.children?.length
            ? item.children.map(convertNavigationItem)
            : undefined,
    };
}
