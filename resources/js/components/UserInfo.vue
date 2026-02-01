<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { getInitials } = useInitials();

const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage
            v-if="auth.user.avatar"
            :src="auth.user.avatar!"
            :alt="auth.user.name"
        />
        <AvatarFallback class="rounded-lg text-foreground">
            {{ getInitials(auth.user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ auth.user.name }}</span>
        <span class="truncate text-xs text-muted-foreground">{{
            auth.user.email
        }}</span>
    </div>
</template>
