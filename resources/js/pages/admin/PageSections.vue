<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    pages: Array<{
        id: number;
        title: string;
        slug: string;
        sections: Array<{
            id: number;
            key: string;
            eyebrow: string | null;
            heading: string;
            body: string | null;
            content: Record<string, any> | null;
            sort_order: number;
        }>;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Page Sections', href: '/admin/page-sections' }],
    },
});

const selectedPageId = ref(props.pages[0]?.id ?? null);
const selectedPage = computed(() => props.pages.find((page) => page.id === selectedPageId.value) ?? props.pages[0]);
const openSection = ref<number | null>(selectedPage.value?.sections?.[0]?.id ?? null);

function contentJson(section: { content: Record<string, any> | null }) {
    return section.content ?? {};
}

function keyValueText(value: Record<string, string> | undefined) {
    return Object.entries(value ?? {})
        .map(([key, body]) => `${key} | ${body}`)
        .join('\n');
}

function signalText(value: Array<Record<string, string>> | undefined) {
    return (value ?? [])
        .map((item) => [item.label, item.title, item.value, item.meta, item.change].filter((part) => part !== undefined).join(' | '))
        .join('\n');
}

function statsText(value: Array<Record<string, string>> | undefined) {
    return (value ?? [])
        .map((item) => `${item.value ?? ''} | ${item.label ?? ''}`)
        .join('\n');
}

function linesText(value: string[] | undefined) {
    return (value ?? []).join('\n');
}

function itemsText(value: Array<Record<string, string>> | undefined) {
    return (value ?? [])
        .map((item) => `${item.title ?? ''} | ${item.body ?? ''}`)
        .join('\n');
}
</script>

<template>
    <Head title="Page Sections" />

    <div class="min-h-full bg-[#050505] p-4 text-white md:p-6">
        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Content management</p>
            <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.08em]">Page Sections</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-white/55">Edit homepage and public page sections without touching code. Hero images and section media are uploaded directly from here.</p>
        </section>

        <section class="mt-6 flex flex-wrap gap-2">
            <button
                v-for="page in pages"
                :key="page.id"
                type="button"
                class="border px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em]"
                :class="selectedPageId === page.id ? 'border-[#c79b55] bg-[#c79b55] text-black' : 'border-white/10 text-white/60'"
                @click="selectedPageId = page.id; openSection = page.sections?.[0]?.id ?? null"
            >
                {{ page.title }}
            </button>
        </section>

        <section class="mt-6 grid gap-4">
            <article v-for="section in selectedPage?.sections || []" :key="section.id" class="border border-[#1a1712] bg-[#0b0b0b]">
                <button type="button" class="flex w-full items-center justify-between gap-4 p-5 text-left" @click="openSection = openSection === section.id ? null : section.id">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c79b55]">{{ section.key }}</p>
                        <h2 class="mt-2 text-xl font-semibold">{{ section.heading }}</h2>
                    </div>
                    <span class="text-sm text-white/45">{{ openSection === section.id ? 'Close' : 'Edit' }}</span>
                </button>

                <div v-if="openSection === section.id" class="grid gap-6 border-t border-white/10 p-5 lg:grid-cols-[1fr_320px]">
                    <Form :action="`/admin/page-sections/${section.id}`" method="post" class="grid gap-4" #default="{ errors, processing, recentlySuccessful }">
                        <input type="hidden" name="_method" value="put" />
                        <label class="grid gap-2 text-sm">
                            <span class="text-white/60">Eyebrow</span>
                            <input name="eyebrow" :value="section.eyebrow" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                        </label>
                        <label class="grid gap-2 text-sm">
                            <span class="text-white/60">Heading</span>
                            <input name="heading" :value="section.heading" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                            <span v-if="errors.heading" class="text-xs text-[#c79b55]">{{ errors.heading }}</span>
                        </label>
                        <label class="grid gap-2 text-sm">
                            <span class="text-white/60">Body</span>
                            <textarea name="body" :value="section.body" rows="4" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                        </label>
                        <div v-if="Object.keys(contentJson(section)).filter((key) => key !== 'image_path').length" class="grid gap-4 border border-white/10 bg-[#101010] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c79b55]">Section content</p>

                            <div v-if="'primary_cta' in contentJson(section) || 'secondary_cta' in contentJson(section)" class="grid gap-4 md:grid-cols-2">
                                <label v-if="'primary_cta' in contentJson(section)" class="grid gap-2 text-sm">
                                    <span class="text-white/60">Primary button text</span>
                                    <input name="content_payload[primary_cta]" :value="section.content?.primary_cta" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                </label>
                                <label v-if="'secondary_cta' in contentJson(section)" class="grid gap-2 text-sm">
                                    <span class="text-white/60">Secondary button text</span>
                                    <input name="content_payload[secondary_cta]" :value="section.content?.secondary_cta" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                </label>
                            </div>

                            <label v-if="'bio' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Bio</span>
                                <textarea name="content_payload[bio]" :value="section.content?.bio" rows="4" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                            </label>

                            <label v-if="'steps' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Approach steps</span>
                                <textarea name="content_payload[steps_text]" :value="keyValueText(section.content?.steps)" rows="6" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                <span class="text-xs text-white/40">One step per line. Format: Title | Description</span>
                            </label>

                            <label v-if="'signals' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Market signal cards</span>
                                <textarea name="content_payload[signals_text]" :value="signalText(section.content?.signals)" rows="7" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                <span class="text-xs text-white/40">One signal per line. Format: Label | Title | Value | Meta | Change</span>
                            </label>

                            <label v-if="'stats' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Stats</span>
                                <textarea name="content_payload[stats_text]" :value="statsText(section.content?.stats)" rows="5" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                <span class="text-xs text-white/40">One stat per line. Format: Value | Label</span>
                            </label>

                            <label v-if="'principles' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Principles</span>
                                <textarea name="content_payload[principles_text]" :value="linesText(section.content?.principles)" rows="5" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                <span class="text-xs text-white/40">One principle per line.</span>
                            </label>

                            <label v-if="'items' in contentJson(section)" class="grid gap-2 text-sm">
                                <span class="text-white/60">Content items</span>
                                <textarea name="content_payload[items_text]" :value="itemsText(section.content?.items)" rows="5" class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]" />
                                <span class="text-xs text-white/40">One item per line. Format: Title | Description</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-4">
                            <button :disabled="processing" class="w-fit bg-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black disabled:opacity-60">Save Section</button>
                            <span v-if="recentlySuccessful" class="text-sm text-[#c79b55]">Saved.</span>
                        </div>
                    </Form>

                    <aside class="border border-white/10 bg-[#111] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c79b55]">Section image</p>
                        <div class="mt-4 aspect-[16/10] overflow-hidden border border-white/10 bg-black">
                            <img v-if="section.content?.image_path" :src="`/storage/${section.content.image_path}`" :alt="section.heading" class="size-full object-cover" />
                            <div v-else class="grid size-full place-items-center text-xs uppercase tracking-[0.16em] text-white/35">No image</div>
                        </div>
                        <Form :action="`/admin/page-sections/${section.id}/image`" method="post" enctype="multipart/form-data" class="mt-4 grid gap-3" #default="{ processing, recentlySuccessful }">
                            <input name="image" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="border border-white/10 bg-[#0b0b0b] px-3 py-2 text-sm text-white" />
                            <button :disabled="processing" class="border border-[#c79b55] px-4 py-2 text-sm text-[#c79b55] disabled:opacity-60">Upload Image</button>
                            <span v-if="recentlySuccessful" class="text-xs text-[#c79b55]">Image updated.</span>
                        </Form>
                    </aside>
                </div>
            </article>
        </section>
    </div>
</template>
