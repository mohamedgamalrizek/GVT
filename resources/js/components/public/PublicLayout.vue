<script setup lang="ts">
import PublicHeader from '@/components/public/PublicHeader.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    title: string;
    description?: string;
}>();

</script>

<template>
    <Head :title="title">
        <meta v-if="description" name="description" :content="description" />
    </Head>

    <div class="min-h-screen bg-[#050505] text-white">
        <PublicHeader />

        <main>
            <slot />
        </main>

        <footer class="border-t border-white/10 px-5 py-10 text-sm text-white/55 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-[1.5fr_1fr_1fr]">
                <div>
                    <p class="text-lg font-semibold text-white">Every investment needs a thesis.</p>
                    <p class="mt-3 max-w-xl">Global Value Thesis is an investment advisory and institutional real estate intelligence platform. Research speaks. Returns follow.</p>
                </div>
                <div class="grid gap-2">
                    <Link href="/about">About</Link>
                    <Link href="/methodology">Methodology</Link>
                    <Link href="/certification">Certification</Link>
                </div>
                <div class="grid gap-2">
                    <Link href="/newsletter">Newsletter</Link>
                    <Link href="/contact">Contact</Link>
                    <Link href="/admin">Admin</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
