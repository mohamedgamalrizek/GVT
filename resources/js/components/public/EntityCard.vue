<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    href: string;
    eyebrow?: string;
    title: string;
    body: string;
    meta?: string;
    image?: string | null;
}>();
</script>

<template>
    <Link :href="href" class="group grid min-h-64 overflow-hidden border border-white/10 bg-white/[0.03] transition hover:border-[#d7b55d]/70 hover:bg-white/[0.06]">
        <div class="relative aspect-[16/10] overflow-hidden border-b border-white/10" v-if="image">
            <img :src="image" :alt="title" class="size-full object-cover opacity-85 transition duration-500 group-hover:scale-105 group-hover:opacity-100" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent" />
        </div>
        <div v-else class="relative aspect-[16/10] overflow-hidden border-b border-white/10 bg-[linear-gradient(135deg,#111,#050505)]">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_72%_30%,rgba(242,207,116,0.22),transparent_32%)]" />
            <div class="absolute bottom-4 left-4 text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">GVT Research</div>
        </div>
        <div class="grid gap-6 p-6">
            <div>
            <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.24em] text-[#f2cf74]">{{ eyebrow }}</p>
            <h3 class="mt-4 text-2xl font-semibold leading-tight text-white group-hover:text-[#f2cf74]">{{ title }}</h3>
            <p class="mt-4 line-clamp-4 text-sm leading-6 text-white/62">{{ body }}</p>
            </div>
            <p v-if="meta" class="self-end border-t border-white/10 pt-4 text-sm text-white/50">{{ meta }}</p>
        </div>
    </Link>
</template>
