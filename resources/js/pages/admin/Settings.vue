<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';

defineProps<{
    settings: {
        brand_name: string;
        short_name: string;
        slogan: string;
        logo_path: string | null;
        favicon_path: string | null;
        contact_email: string;
        contact_phone: string | null;
        contact_address: string | null;
        linkedin_url: string | null;
        x_url: string | null;
        instagram_url: string | null;
        default_seo_title: string;
        default_seo_description: string;
        default_seo_keywords: string | null;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Settings', href: '/admin/settings' }],
    },
});
</script>

<template>
    <Head title="Website Settings" />

    <div class="min-h-full bg-[#050505] p-4 text-white md:p-6">
        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Control center</p>
            <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.08em]">Website Settings</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-white/55">Manage brand identity, logo, favicon, contact data, and default SEO metadata. Changes are shared across public and admin pages.</p>
        </section>

        <Form action="/admin/settings" method="post" enctype="multipart/form-data" class="mt-6 grid gap-6" #default="{ errors, processing, recentlySuccessful }">
            <input type="hidden" name="_method" value="put" />
            <section class="grid gap-5 border border-[#1a1712] bg-[#0b0b0b] p-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Brand</p>
                </div>

                <label class="grid gap-2 text-sm">
                    <span class="text-white/70">Brand name</span>
                    <input name="brand_name" :value="settings.brand_name" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                    <span v-if="errors.brand_name" class="text-xs text-[#c79b55]">{{ errors.brand_name }}</span>
                </label>

                <label class="grid gap-2 text-sm">
                    <span class="text-white/70">Short name</span>
                    <input name="short_name" :value="settings.short_name" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                </label>

                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="text-white/70">Slogan</span>
                    <input name="slogan" :value="settings.slogan" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                </label>

                <div class="grid gap-3">
                    <span class="text-sm text-white/70">Logo</span>
                    <img v-if="settings.logo_path" :src="`/storage/${settings.logo_path}`" :alt="settings.brand_name" class="h-16 w-fit max-w-xs border border-white/10 bg-black p-3 object-contain" />
                    <input name="logo" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white" />
                </div>

                <div class="grid gap-3">
                    <span class="text-sm text-white/70">Favicon</span>
                    <img v-if="settings.favicon_path" :src="`/storage/${settings.favicon_path}`" alt="Favicon" class="size-14 border border-white/10 bg-black p-2 object-contain" />
                    <input name="favicon" type="file" accept="image/x-icon,image/png,image/svg+xml,image/webp" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white" />
                </div>
            </section>

            <section class="grid gap-5 border border-[#1a1712] bg-[#0b0b0b] p-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Contact</p>
                </div>
                <input name="contact_email" :value="settings.contact_email" placeholder="Email" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <input name="contact_phone" :value="settings.contact_phone" placeholder="Phone" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <input name="contact_address" :value="settings.contact_address" placeholder="Address" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55] lg:col-span-2" />
                <input name="linkedin_url" :value="settings.linkedin_url" placeholder="LinkedIn URL" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <input name="x_url" :value="settings.x_url" placeholder="X URL" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <input name="instagram_url" :value="settings.instagram_url" placeholder="Instagram URL" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
            </section>

            <section class="grid gap-5 border border-[#1a1712] bg-[#0b0b0b] p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">SEO defaults</p>
                <input name="default_seo_title" :value="settings.default_seo_title" placeholder="Default SEO title" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <textarea name="default_seo_description" :value="settings.default_seo_description" rows="3" placeholder="Default SEO description" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                <input name="default_seo_keywords" :value="settings.default_seo_keywords" placeholder="SEO keywords" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
            </section>

            <div class="flex items-center gap-4">
                <button :disabled="processing" class="bg-[#c79b55] px-6 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black disabled:opacity-60">Save Settings</button>
                <p v-if="recentlySuccessful" class="text-sm text-[#c79b55]">Settings saved.</p>
            </div>
        </Form>
    </div>
</template>
