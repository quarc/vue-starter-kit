<?php

namespace App\Navigation\Sections;

use App\Navigation\Items\DynamicItem;
use App\Navigation\Support\BaseSection;

class PresentationSection extends BaseSection
{
    public function label(): string
    {
        return 'Quarc Presentation';
    }

    public function items(): array
    {
        return [
            new DynamicItem(
                name: 'dashboard',
                label: 'Dashboard',
                icon: 'layout-dashboard',
                route: 'dashboard',
                order: 10
            ),
            new DynamicItem(
                name: 'platform',
                label: 'Platform',
                icon: 'layers',
                children: [
                    new DynamicItem(name: 'projects', label: 'Projects', icon: 'folder-kanban', route: null, description: 'Manage your active projects'),
                    new DynamicItem(name: 'teams', label: 'Teams', icon: 'users', route: null, description: 'Team members and roles'),
                    new DynamicItem(name: 'integrations', label: 'Integrations', icon: 'network', badge: 'New', description: 'Connect external tools'),
                ],
                order: 20
            ),
            new DynamicItem(
                name: 'features',
                label: 'Features',
                icon: 'zap',
                children: [
                    new DynamicItem(
                        name: 'auth',
                        label: 'Authentication',
                        icon: 'shield-check',
                        children: [
                            new DynamicItem(name: 'sso', label: 'SSO', icon: 'key', description: 'Single Sign-On settings'),
                            new DynamicItem(name: 'mfa', label: '2FA', icon: 'smartphone', description: 'Two-Factor Authentication'),
                        ]
                    ),
                    new DynamicItem(name: 'billing', label: 'Billing & Invoices', icon: 'credit-card', description: 'Manage plan and payments'),
                    new DynamicItem(name: 'notifications', label: 'Notifications', icon: 'bell', badge: 12, description: 'User alerts and preferences'),
                ],
                order: 30
            ),
            new DynamicItem(
                name: 'docs',
                label: 'Documentation',
                icon: 'book-open',
                route: null,
                order: 40
            ),
            new DynamicItem(
                name: 'settings',
                label: 'Settings',
                icon: 'settings',
                route: 'profile.edit',
                order: 90
            ),
        ];
    }
}
