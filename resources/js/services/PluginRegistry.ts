import { type Component, type DefineComponent, reactive } from 'vue';

export type PluginDefinition = {
    component: Component | DefineComponent;
    priority: number;
};

// Reactive registry to ensure Vue updates when plugins load
const registry = reactive<Record<string, PluginDefinition[]>>({});

export const PluginRegistry = {
    /**
     * Register a component to a specific slot.
     * @param slotName The name of the slot (e.g., 'sidebar.header.dropdown')
     * @param component The Vue component to render
     * @param priority Higher priority renders first (default: 10)
     */
    register(slotName: string, component: Component | DefineComponent, priority: number = 10) {
        if (!registry[slotName]) {
            registry[slotName] = [];
        }

        registry[slotName].push({
            component,
            priority,
        });

        // Ensure array is always sorted by priority
        registry[slotName].sort((a, b) => b.priority - a.priority);
    },

    /**
     * Get all plugins registered for a slot.
     */
    getPlugins(slotName: string): PluginDefinition[] {
        return registry[slotName] || [];
    },
    
    /**
     * Check if any plugins are registered for a slot.
     */
    hasPlugins(slotName: string): boolean {
        return (registry[slotName]?.length || 0) > 0;
    }
};
