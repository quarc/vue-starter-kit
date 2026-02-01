<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import {
    destroy as destroyAvatar,
    store as storeAvatar,
} from '@/routes/avatar';
import { useForm, usePage } from '@inertiajs/vue3';
import { Loader2, Trash2, UploadCloud } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
const page = usePage();
const user = computed(() => page.props.auth.user);

// Avatar upload state
const avatarFileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const isDragging = ref(false);
const uploadForm = useForm({
    avatar: null as File | null,
});
const deleteForm = useForm({});

const previewAvatar = computed(() =>
    selectedFile.value
        ? URL.createObjectURL(selectedFile.value)
        : user.value.avatar,
);

const triggerFileDialog = () => {
    avatarFileInput.value?.click();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement | null;
    if (target?.files?.length) {
        selectedFile.value = target.files[0];
        uploadAvatar();
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = false;
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = false;
    const files = event.dataTransfer?.files;
    if (files?.length) {
        selectedFile.value = files[0];
        uploadAvatar();
    }
};

const uploadAvatar = () => {
    if (!selectedFile.value) return;

    uploadForm.avatar = selectedFile.value;
    uploadForm.post(storeAvatar().url, {
        preserveScroll: true,
        onFinish: () => {
            selectedFile.value = null;
            if (avatarFileInput.value) {
                avatarFileInput.value.value = '';
            }
        },
    });
};

const deleteAvatar = () => {
    deleteForm.delete(destroyAvatar().url, {
        preserveScroll: true,
    });
};

defineProps<{
    status?: string;
}>();
</script>

<template>
    <div class="flex flex-col space-y-6">
        <HeadingSmall
            :title="$t('Avatar')"
            :description="$t('Upload your profile picture')"
        />

        <div class="space-y-4">
            <div class="flex items-start gap-4">
                <div
                    class="group relative cursor-pointer"
                    @click="triggerFileDialog"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @drop="handleDrop"
                    :class="{ 'scale-105': isDragging }"
                >
                    <div
                        class="flex size-16 items-center justify-center overflow-hidden rounded-lg border border-dashed border-muted-foreground/30 bg-muted/20 transition-all group-hover:border-muted-foreground"
                    >
                        <img
                            v-if="previewAvatar"
                            :src="previewAvatar"
                            :alt="user.name"
                            class="h-full w-full object-cover"
                        />
                        <span
                            v-else
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            {{
                                user.name
                                    .split(' ')
                                    .map((s) => s.charAt(0))
                                    .slice(0, 2)
                                    .join('')
                                    .toUpperCase()
                            }}
                        </span>
                    </div>

                    <div
                        class="absolute inset-[1px] flex items-center justify-center rounded-lg bg-black/40 opacity-0 transition-opacity group-hover:opacity-100"
                        :class="{ 'opacity-100': uploadForm.processing }"
                    >
                        <Loader2
                            v-if="uploadForm.processing"
                            class="h-5 w-5 animate-spin text-white"
                        />
                        <UploadCloud v-else class="h-5 w-5 text-white" />
                    </div>

                    <button
                        v-if="user.avatar"
                        class="absolute -top-2 -right-2 flex size-6 items-center justify-center rounded-full bg-destructive text-destructive-foreground opacity-0 shadow-sm transition-all group-hover:opacity-100 hover:bg-destructive/90"
                        :disabled="deleteForm.processing"
                        @click.stop="deleteAvatar"
                        type="button"
                    >
                        <Loader2
                            v-if="deleteForm.processing"
                            class="h-3 w-3 animate-spin"
                        />
                        <Trash2 v-else class="h-3 w-3" />
                    </button>
                </div>

                <div class="flex flex-1 flex-col gap-1">
                    <div class="space-y-1">
                        <p class="text-sm font-medium">
                            {{ $t('Click or drag to upload') }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ $t('PNG, JPG up to 2MB') }}
                        </p>
                    </div>
                    <InputError :message="uploadForm.errors.avatar" />
                    <p
                        v-if="status === 'avatar-updated'"
                        class="text-sm text-neutral-600"
                    >
                        {{ $t('Avatar updated.') }}
                    </p>
                    <p
                        v-if="status === 'avatar-deleted'"
                        class="text-sm text-neutral-600"
                    >
                        {{ $t('Avatar removed.') }}
                    </p>
                </div>
            </div>

            <input
                ref="avatarFileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileChange"
            />
        </div>
    </div>
</template>
