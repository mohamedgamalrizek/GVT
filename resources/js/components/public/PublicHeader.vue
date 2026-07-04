<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from '@lucide/vue';
import { computed, ref } from 'vue';

defineProps<{
    overlay?: boolean;
}>();

const open = ref(false);
const page = usePage();
const settings = computed(() => page.props.siteSettings);

const nav = [
    { label: 'Home', href: '/', match: ['/'] },
    { label: 'Investment Theses', href: '/theses', match: ['/theses'] },
    { label: 'Positions', href: '/positions', match: ['/positions'] },
    { label: 'Our Approach', href: '/methodology', match: ['/methodology'] },
    { label: 'Certification', href: '/certification', match: ['/certification'] },
    { label: 'News', href: '/insights', match: ['/insights'] },
];

const currentPath = computed(() => {
    const url = page.url.split('?')[0] || '/';

    return url.endsWith('/') && url !== '/' ? url.slice(0, -1) : url;
});

function isActive(item: { href: string; match: string[] }) {
    if (item.href === '/') {
        return currentPath.value === '/';
    }

    return item.match.some((path) => currentPath.value === path || currentPath.value.startsWith(`${path}/`));
}

const logoUrl = computed(() => (settings.value.logo_path ? `/storage/${settings.value.logo_path}` : null));
const brandFirstLine = computed(() => settings.value.brand_name.split(' ').slice(0, 2).join(' '));
const brandSecondLine = computed(() => settings.value.brand_name.split(' ').slice(2).join(' ') || settings.value.short_name);
</script>

<template>
    <header
        class="z-40 border-b border-[#1a1712] bg-[#050505]/92 backdrop-blur-xl"
        :class="overlay ? 'relative border-transparent bg-transparent backdrop-blur-0' : 'sticky top-0'"
    >
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 text-[11px] uppercase tracking-[0.18em] text-white/60">
            <Link href="/" class="flex items-center gap-3">
                <img v-if="logoUrl" :src="logoUrl" :alt="settings.brand_name" class="h-9 w-auto max-w-[160px] object-contain" />
                <div v-else class="flex h-8 items-end gap-1 text-[#c79b55]">
                    <span class="h-5 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                    <span class="h-7 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                    <span class="h-6 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                </div>
                <span v-if="!logoUrl" class="font-semibold leading-tight text-white">{{ brandFirstLine }}<br />{{ brandSecondLine }}</span>
            </Link>

            <nav class="hidden items-center gap-10 lg:flex">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="relative py-2 transition hover:text-[#c79b55]"
                    :class="isActive(item) ? 'text-[#c79b55]' : 'text-white/60'"
                >
                    {{ item.label }}
                    <span v-if="isActive(item)" class="absolute -bottom-1 left-0 h-px w-full bg-[#c79b55]" />
                </Link>
            </nav>

            <Link href="/admin" class="hidden border border-white/25 px-5 py-3 text-white transition hover:border-[#c79b55] hover:text-[#c79b55] lg:inline-flex">Investor Portal</Link>

            <button class="text-white lg:hidden" type="button" @click="open = !open" aria-label="Toggle navigation">
                <X v-if="open" class="size-5" />
                <Menu v-else class="size-5" />
            </button>
        </div>

        <nav v-if="open" class="grid gap-1 border-t border-[#1a1712] bg-[#070707] px-6 py-5 text-[11px] uppercase tracking-[0.18em] text-white/65 lg:hidden">
            <Link
                v-for="item in nav"
                :key="item.href"
                :href="item.href"
                class="py-3"
                :class="isActive(item) ? 'text-[#c79b55]' : 'text-white/65'"
                @click="open = false"
            >
                {{ item.label }}
            </Link>
            <Link href="/admin" class="mt-3 border border-white/25 px-4 py-3 text-center text-white" @click="open = false">Investor Portal</Link>
        </nav>
    </header>
</template>
