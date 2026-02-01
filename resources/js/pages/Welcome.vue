<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import { useAppearance } from '@/composables/useAppearance';

import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();

const isDark = computed(() => {
    if (appearance.value === 'system') {
        return typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    return appearance.value === 'dark';
});

function toggleTheme() {
    const nextTheme = isDark.value ? 'light' : 'dark';
    updateAppearance(nextTheme);
}

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Quarc Starter Kit" />
    <div
        class="relative flex min-h-screen flex-col items-center justify-between bg-background p-4 text-foreground dark:bg-black lg:p-8"
    >
        <header class="w-full z-20">
            <div class="mx-auto flex max-w-6xl items-center justify-end p-4 lg:p-8">
                <nav v-if="canLogin" class="flex justify-end gap-4">
                    <button
                        @click="toggleTheme"
                        class="rounded-md p-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
                    >
                        <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    </button>

                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard().url"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    {{ $t('Dashboard') }}
                </Link>

                <template v-else>
                    <Link
                        :href="login().url"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        {{ $t('Log in') }}
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="register().url"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        {{ $t('Register') }}
                    </Link>
                    </template>
            </nav>
            </div>
        </header>
        <div class="grid w-full max-w-6xl grid-cols-1 gap-4 lg:h-[400px] lg:grid-cols-2">
            <div
                class="group bg-card shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] relative flex flex-col justify-between overflow-hidden rounded-xl p-8 lg:p-12 transition-all hover:bg-accent/50"
            >
                <!-- Background Logo -->
                <div class="absolute -right-20 -top-20 h-[120%] w-auto opacity-[0.04] pointer-events-none rotate-10">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="50 50 300 309" class="h-full w-full text-foreground">
                        <path stroke="none" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M176.289 51.868 C 126.730 80.727,129.825 77.088,146.711 86.637 C 153.691 90.585,161.810 95.206,164.754 96.907 C 167.698 98.608,174.745 102.672,180.414 105.938 C 186.083 109.204,208.545 122.536,230.328 135.564 C 262.684 154.916,270.909 159.008,275.260 157.916 C 280.857 156.511,335.976 125.491,336.037 123.711 C 336.056 123.144,327.028 117.503,315.974 111.176 C 304.920 104.849,273.883 86.988,247.003 71.485 C 191.673 39.575,195.552 40.651,176.289 51.868 M89.691 103.170 C 72.680 113.203,58.763 122.498,58.763 123.827 C 58.763 126.090,91.056 145.847,114.672 158.032 L 125.220 163.474 150.754 148.453 C 189.408 125.714,189.693 125.535,189.665 124.059 C 189.636 122.470,179.381 115.612,162.371 105.805 C 117.870 80.147,128.097 80.518,89.691 103.170 M55.670 211.260 C 55.670 298.502,52.555 287.868,83.090 304.874 C 93.524 310.685,106.016 317.824,110.850 320.738 L 119.637 326.037 119.097 248.158 L 118.557 170.278 106.186 163.296 C 99.381 159.455,89.639 153.950,84.536 151.063 C 71.201 143.516,57.236 136.082,56.394 136.082 C 55.996 136.082,55.670 169.912,55.670 211.260 M308.247 151.125 C 293.505 159.396,279.356 167.506,276.804 169.145 C 272.289 172.046,272.165 173.074,272.165 207.498 L 272.165 242.869 282.580 249.552 C 294.459 257.175,291.492 257.870,320.601 240.653 C 342.342 227.794,341.966 228.701,342.135 188.660 C 342.266 157.478,340.311 136.082,337.331 136.084 C 336.077 136.085,322.990 142.853,308.247 151.125 M237.855 265.626 C 225.222 273.191,212.797 280.820,210.244 282.578 C 205.709 285.699,206.316 286.205,236.216 304.227 L 266.831 322.680 267.578 339.175 C 268.915 368.676,274.705 370.600,307.274 352.362 C 335.793 336.391,334.276 338.711,333.577 312.109 L 332.990 289.762 301.119 270.654 C 263.415 248.049,266.761 248.315,237.855 265.626 M129.897 292.527 L 129.897 331.446 151.031 343.846 C 202.540 374.068,194.647 372.863,224.765 355.106 C 256.969 336.119,257.732 335.575,257.732 331.603 C 257.732 328.525,247.868 319.588,244.470 319.588 C 243.763 319.588,227.206 309.985,207.675 298.248 C 159.236 269.138,132.607 253.608,131.132 253.608 C 130.453 253.608,129.897 271.122,129.897 292.527 "></path>
                    </svg>
                </div>
                <div class="z-10">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="50 50 300 309" class="h-10 w-10 text-foreground">
                                <path stroke="none" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M176.289 51.868 C 126.730 80.727,129.825 77.088,146.711 86.637 C 153.691 90.585,161.810 95.206,164.754 96.907 C 167.698 98.608,174.745 102.672,180.414 105.938 C 186.083 109.204,208.545 122.536,230.328 135.564 C 262.684 154.916,270.909 159.008,275.260 157.916 C 280.857 156.511,335.976 125.491,336.037 123.711 C 336.056 123.144,327.028 117.503,315.974 111.176 C 304.920 104.849,273.883 86.988,247.003 71.485 C 191.673 39.575,195.552 40.651,176.289 51.868 M89.691 103.170 C 72.680 113.203,58.763 122.498,58.763 123.827 C 58.763 126.090,91.056 145.847,114.672 158.032 L 125.220 163.474 150.754 148.453 C 189.408 125.714,189.693 125.535,189.665 124.059 C 189.636 122.470,179.381 115.612,162.371 105.805 C 117.870 80.147,128.097 80.518,89.691 103.170 M55.670 211.260 C 55.670 298.502,52.555 287.868,83.090 304.874 C 93.524 310.685,106.016 317.824,110.850 320.738 L 119.637 326.037 119.097 248.158 L 118.557 170.278 106.186 163.296 C 99.381 159.455,89.639 153.950,84.536 151.063 C 71.201 143.516,57.236 136.082,56.394 136.082 C 55.996 136.082,55.670 169.912,55.670 211.260 M308.247 151.125 C 293.505 159.396,279.356 167.506,276.804 169.145 C 272.289 172.046,272.165 173.074,272.165 207.498 L 272.165 242.869 282.580 249.552 C 294.459 257.175,291.492 257.870,320.601 240.653 C 342.342 227.794,341.966 228.701,342.135 188.660 C 342.266 157.478,340.311 136.082,337.331 136.084 C 336.077 136.085,322.990 142.853,308.247 151.125 M237.855 265.626 C 225.222 273.191,212.797 280.820,210.244 282.578 C 205.709 285.699,206.316 286.205,236.216 304.227 L 266.831 322.680 267.578 339.175 C 268.915 368.676,274.705 370.600,307.274 352.362 C 335.793 336.391,334.276 338.711,333.577 312.109 L 332.990 289.762 301.119 270.654 C 263.415 248.049,266.761 248.315,237.855 265.626 M129.897 292.527 L 129.897 331.446 151.031 343.846 C 202.540 374.068,194.647 372.863,224.765 355.106 C 256.969 336.119,257.732 335.575,257.732 331.603 C 257.732 328.525,247.868 319.588,244.470 319.588 C 243.763 319.588,227.206 309.985,207.675 298.248 C 159.236 269.138,132.607 253.608,131.132 253.608 C 130.453 253.608,129.897 271.122,129.897 292.527 "></path>
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold tracking-tight text-foreground lg:text-3xl">Quarc Starter Kit</h1>
                    </div>
                    
                    <div class="mt-4">
                        <p class="max-w-md text-sm text-muted-foreground">
                            A modern, production-ready Laravel and Vue starter kit for rapid development. Everything you need to build robust applications from day one.
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-md border border-border bg-background">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog"><circle cx="18" cy="15" r="3"/><circle cx="9" cy="7" r="4"/><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="m21.7 16.4.9-.3"/><path d="m15.2 13.9-.9-.3"/><path d="m16.6 18.7.3-.9"/><path d="m19.1 12.2.3-.9"/><path d="m19.6 18.7-.4-1"/><path d="m16.8 12.3-.4-1"/><path d="m14.3 16.6 1-.4"/><path d="m20.7 13.8 1-.4"/></svg>
                            </div>
                            <span class="text-sm font-medium">Advanced User Settings</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-md border border-border bg-background">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-palette"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>
                            </div>
                            <span class="text-sm font-medium">Flexible Design System</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-md border border-border bg-background">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                            </div>
                            <span class="text-sm font-medium">Seamless Localization</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-md border border-border bg-background">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            </div>
                            <span class="text-sm font-medium">Real-time Notifications</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 z-10 flex gap-4">
                     <a
                        href="https://github.com/quarc/vue-starter-kit"
                        target="_blank"
                        class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        {{ $t('Documentation') }}
                    </a>
                    <a
                        href="https://github.com/quarc/vue-starter-kit"
                        target="_blank"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-transparent px-8 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-github"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                        GitHub
                    </a>
                </div>

                <!-- Abstract Decorative Lines -->
                <div class="absolute right-0 top-0 h-full w-full opacity-[0.03] dark:opacity-[0.05] pointer-events-none">
                     <svg width="100%" height="100%">
                        <defs>
                            <pattern id="grid-pattern" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid-pattern)" />
                    </svg>
                </div>
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-4">
                <!-- Laravel Block -->
                <div
                    class="group relative flex flex-1 overflow-hidden rounded-xl bg-card shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] transition-all hover:bg-accent/50"
                >
                     <!-- Background Logo -->
                    <div class="absolute -right-20 -top-20 h-[150%] w-auto opacity-[0.07] pointer-events-none rotate-345">
                         <svg
                            viewBox="0 0 1888 1888" 
                            fill="currentColor"
                            class="h-full w-full text-[#FF2D20]"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M791.5 1714L215 1381.5c-8.5-5.5-15-8.5-15-19.5V357.5c0-8.158 5-13.5 9.5-16L502 173c9.5-5.5 17.5-5.5 26.5 0L819 340c11.5 6.5 12 15 12 22.5v622L1073.5 845V527c0-11 5-17.5 17-24.5L1380 336c7-4 12.5-4 19.5 0l295 170c9.5 5.5 10.5 12 10.5 21.5V858c0 10.5-2.5 16-13 22.5l-278.5 160v317c0 12.5-3 17.5-14 24L821 1714c-11 6-18.5 6-29.5 0zm-9-61.5v-279l-276-156c-9-5.5-15.5-9.5-15.5-23V543L248 403.5V1345zm583-307.5v-277L831 1373.5v279zm-25.528-318.167L1098 886.5 565 1194l241 137zM782.5 1012V403L540 543v609zm583-28V708l-243-140v277zm291-139V568l-243 140v276zm-267-179.5l242-139.5-242-139.5L1147 526zM757.635 361.004L515 221.5 273 361l242 140z"/>
                        </svg>
                    </div>

                    <div class="relative z-10 flex w-full flex-col justify-between p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <svg
                                    viewBox="0 0 1888 1888"
                                    class="h-10 w-10 text-[#FF2D20]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                >
                                    <path d="M791.5 1714L215 1381.5c-8.5-5.5-15-8.5-15-19.5V357.5c0-8.158 5-13.5 9.5-16L502 173c9.5-5.5 17.5-5.5 26.5 0L819 340c11.5 6.5 12 15 12 22.5v622L1073.5 845V527c0-11 5-17.5 17-24.5L1380 336c7-4 12.5-4 19.5 0l295 170c9.5 5.5 10.5 12 10.5 21.5V858c0 10.5-2.5 16-13 22.5l-278.5 160v317c0 12.5-3 17.5-14 24L821 1714c-11 6-18.5 6-29.5 0zm-9-61.5v-279l-276-156c-9-5.5-15.5-9.5-15.5-23V543L248 403.5V1345zm583-307.5v-277L831 1373.5v279zm-25.528-318.167L1098 886.5 565 1194l241 137zM782.5 1012V403L540 543v609zm583-28V708l-243-140v277zm291-139V568l-243 140v276zm-267-179.5l242-139.5-242-139.5L1147 526zM757.635 361.004L515 221.5 273 361l242 140z"/>
                                </svg>
                                <h2 class="text-xl font-bold">Laravel 12</h2>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="mb-4 text-sm text-muted-foreground">
                                {{ 'Laravel has an incredibly rich ecosystem. We suggest starting with the following.' }}
                            </p>
                            <div class="relative mt-2 lg:ml-1">
                                <!-- Connecting Line -->
                                <div class="absolute left-[5px] top-2 h-[calc(100%-16px)] w-px bg-border"></div>

                                <div class="relative flex flex-col gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#FF2D20]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#FF2D20]"></div>
                                        </div>
                                        <div class="text-sm text-muted-foreground">
                                            Read the <a href="https://laravel.com/docs" target="_blank" class="font-medium text-[#FF2D20] hover:underline">Documentation <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#FF2D20]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#FF2D20]"></div>
                                        </div>
                                        <div class="text-sm text-muted-foreground">
                                            Watch video tutorials at <a href="https://laracasts.com" target="_blank" class="font-medium text-[#FF2D20] hover:underline">Laracasts <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decorative background element for Laravel -->
                    <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full bg-[#FF2D20]/5 blur-3xl transition-all group-hover:bg-[#FF2D20]/10"></div>
                </div>

                <!-- Bottom Row: Vue & Tailwind -->
                <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Vue Block -->
                    <div
                        class="group relative flex flex-col justify-between overflow-hidden rounded-xl bg-card shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 transition-all hover:bg-accent/50"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center">
                                <svg
                                    viewBox="0 0 261 226" 
                                    class="h-8 w-8"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path d="M161.096 0.00100708L130.5 53.001L99.904 0.00100708H0L130.5 226.113L261 0.00100708H161.096Z" fill="#42b883"/>
                                    <path d="M161.096 0.00100708L130.5 53.001L99.904 0.00100708H52.2L130.5 135.586L208.8 0.00100708H161.096Z" fill="#35495E"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg">Vue 3</h3>
                        </div>
                        <div class="mt-4">
                            <p class="mb-4 text-xs text-muted-foreground">Versatile framework for building web user interfaces.</p>
                             <div class="relative mt-2 lg:ml-1">
                                <!-- Connecting Line -->
                                <div class="absolute left-[5px] top-2 h-[calc(100%-16px)] w-px bg-border"></div>

                                <div class="relative flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#42b883]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#42b883]"></div>
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Read the <a href="https://vuejs.org/guide/introduction" target="_blank" class="font-medium text-[#42b883] hover:underline">Documentation <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#42b883]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#42b883]"></div>
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Visit the <a href="https://vuejs.org" target="_blank" class="font-medium text-[#42b883] hover:underline">Website <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tailwind Block -->
                    <div
                        class="group relative flex flex-col justify-between overflow-hidden rounded-xl bg-card shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 transition-all hover:bg-accent/50"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center">
                                <svg
                                    viewBox="0 0 54 33"
                                    fill="currentColor"
                                    class="h-8 w-8 text-[#38bdf8]"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg">Tailwind 4</h3>
                        </div>
                        <div class="mt-4">
                            <p class="mb-4 text-xs text-muted-foreground">Rapidly build modern websites without leaving HTML.</p>
                             <div class="relative mt-2 lg:ml-1">
                                <!-- Connecting Line -->
                                <div class="absolute left-[5px] top-2 h-[calc(100%-16px)] w-px bg-border"></div>

                                <div class="relative flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#38bdf8]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#38bdf8]"></div>
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Read the <a href="https://tailwindcss.com/docs/installation" target="_blank" class="font-medium text-[#38bdf8] hover:underline">Documentation <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="relative z-10 flex h-3 w-3 items-center justify-center rounded-full bg-[#38bdf8]/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-[#38bdf8]"></div>
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Visit the  <a href="https://tailwindcss.com" target="_blank" class="font-medium text-[#38bdf8] hover:underline">Website <span class="text-[10px]">&nearr;</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="py-4 text-center text-xs text-muted-foreground lg:py-8">
            &copy; {{ new Date().getFullYear() }} Quarc. All rights reserved.
        </div>
    </div>
</template>
