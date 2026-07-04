<script setup lang="ts">
import PublicHeader from '@/components/public/PublicHeader.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    title: string;
    description?: string;
}>();

const page = usePage();
const settings = computed(() => page.props.siteSettings);
const logoUrl = computed(() => (settings.value.logo_path ? `/storage/${settings.value.logo_path}` : null));
const brandFirstLine = computed(() => settings.value.brand_name.split(' ').slice(0, 2).join(' '));
const brandSecondLine = computed(() => settings.value.brand_name.split(' ').slice(2).join(' ') || settings.value.short_name);
</script>

<template>
    <Head :title="title">
        <meta name="description" :content="description || settings.default_seo_description" />
        <meta name="keywords" :content="settings.default_seo_keywords || ''" />
        <meta property="og:title" :content="title || settings.default_seo_title" />
        <meta property="og:description" :content="description || settings.default_seo_description" />
    </Head>

    <div class="min-h-screen bg-[#050505] text-white">
        <PublicHeader />

        <main>
            <slot />
        </main>

        <footer class="border-t border-white/10 px-5 py-12 text-sm text-white/55 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 md:grid-cols-[1.5fr_1fr_1fr]">
                <div>
                    <Link href="/" class="inline-flex items-center gap-3">
                        <img v-if="logoUrl" :src="logoUrl" :alt="settings.brand_name" class="h-11 w-auto max-w-[190px] object-contain" />
                        <template v-else>
                            <div class="flex h-9 items-end gap-1 text-[#c79b55]">
                                <span class="h-5 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                                <span class="h-8 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                                <span class="h-6 w-1.5 skew-x-[-12deg] bg-[#c79b55]" />
                            </div>
                            <span class="font-semibold leading-tight text-white">{{ brandFirstLine }}<br />{{ brandSecondLine }}</span>
                        </template>
                    </Link>
                    <p class="mt-5 text-lg font-semibold text-white">{{ settings.slogan }}</p>
                    <p class="mt-3 max-w-xl">An investment advisory and institutional real estate intelligence platform. Research speaks. Returns follow.</p>
                </div>
                <div class="grid gap-2">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Solutions</p>
                    <Link href="/theses" class="transition hover:text-[#c79b55]">Investment Theses</Link>
                    <Link href="/positions" class="transition hover:text-[#c79b55]">Positions</Link>
                    <Link href="/methodology" class="transition hover:text-[#c79b55]">Our Approach</Link>
                </div>
                <div class="grid gap-2">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Company</p>
                    <Link href="/about" class="transition hover:text-[#c79b55]">About Us</Link>
                    <Link href="/certification" class="transition hover:text-[#c79b55]">Certification</Link>
                    <Link href="/insights" class="transition hover:text-[#c79b55]">News</Link>
                    <Link href="/contact" class="mt-3 transition hover:text-[#c79b55]">Contact</Link>
                    <span>{{ settings.contact_email }}</span>
                    <span v-if="settings.contact_phone">{{ settings.contact_phone }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>
