<script setup lang="ts">
import { computed } from 'vue';
import { PluginRegistry } from '@/services/PluginRegistry';

const props = defineProps<{
    name: string;
    context?: Record<string, any>;
}>();

const plugins = computed(() => PluginRegistry.getPlugins(props.name));
</script>

<template>
    <component
        v-for="(plugin, index) in plugins"
        :key="index"
        :is="plugin.component"
        v-bind="context"
    />
</template>
