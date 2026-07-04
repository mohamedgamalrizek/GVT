<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    resource: string;
    title: string;
    items: any;
    meta: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin', href: '/admin' }],
    },
});

const selected = ref<any | null>(null);
const payload = ref('{}');

const rows = computed(() => props.items.data ?? []);
const keys = computed(() => {
    const sample = rows.value[0];
    return sample ? Object.keys(sample).filter((key) => !['uuid', 'deleted_at'].includes(key)).slice(0, 7) : ['id'];
});

function edit(item: any) {
    selected.value = item;
    const copy = { ...item };
    delete copy.roles;
    delete copy.permissions;
    delete copy.created_at;
    delete copy.updated_at;
    payload.value = JSON.stringify(copy, null, 2);
}

function createNew() {
    selected.value = null;
    payload.value = JSON.stringify(samplePayload(props.resource), null, 2);
}

function samplePayload(resource: string) {
    const samples: Record<string, any> = {
        developers: { name: 'Evidence Capital Communities', slug: 'evidence-capital-communities', certification_status: 'under_review', rating_score: 70, summary: 'Developer summary.', evaluation_summary: 'Evaluation summary.', published_at: new Date().toISOString() },
        positions: { developer_id: props.meta.developers?.[0]?.id, project_name: 'Evidence-Led Position', slug: 'evidence-led-position', location: 'Cairo, Egypt', city: 'Cairo', asset_type: 'Residential', risk_level: 'Measured', certification_status: 'Under Review', thesis_summary: 'Thesis summary.', investment_rationale: 'Investment rationale.', published_at: new Date().toISOString() },
        theses: { developer_id: props.meta.developers?.[0]?.id, title: 'Evidence-Led Thesis', slug: 'evidence-led-thesis', market: 'Egypt', city: 'Cairo', category: 'Residential', status: 'published', executive_summary: 'Executive summary.', market_context: 'Market context.', asset_rationale: 'Asset rationale.', risk_notes: 'Risk notes.', published_at: new Date().toISOString() },
        blog: { blog_category_id: props.meta.categories?.[0]?.id, title: 'Research Note', slug: 'research-note', excerpt: 'Short excerpt.', body: 'Body.', status: 'published', published_at: new Date().toISOString() },
        settings: { key: 'example', value: { enabled: true } },
    };

    return samples[resource] ?? { name: 'New record' };
}
</script>

<template>
    <Head :title="title" />
    <div class="grid min-h-full gap-6 bg-[#050505] p-4 text-white md:p-6">
        <section class="flex flex-col justify-between gap-4 border border-[#1a1712] bg-[#0b0b0b] p-6 md:flex-row md:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Admin resource</p>
                <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.08em]">{{ title }}</h1>
            </div>
            <button class="border border-[#c79b55] px-4 py-2 text-sm text-[#c79b55]" type="button" @click="createNew">New record</button>
        </section>

        <section class="overflow-hidden border border-[#1a1712] bg-[#0b0b0b]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#111] text-white/50">
                        <tr>
                            <th v-for="key in keys" :key="key" class="px-4 py-3 font-medium">{{ key }}</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in rows" :key="item.id" class="border-t border-white/10">
                            <td v-for="key in keys" :key="key" class="max-w-64 truncate px-4 py-3">{{ typeof item[key] === 'object' ? JSON.stringify(item[key]) : item[key] }}</td>
                            <td class="flex gap-2 px-4 py-3">
                                <button class="text-[#c79b55] underline" type="button" @click="edit(item)">Edit</button>
                                <Form :action="`/admin/${resource}/${item.id}`" method="delete">
                                    <button class="text-destructive underline" type="submit">Delete</button>
                                </Form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <h2 class="text-xl font-semibold">{{ selected ? 'Edit record' : 'Create record' }}</h2>
            <div v-if="resource === 'theses' && selected" class="mt-4 rounded-md border bg-muted/20 p-4">
                <p class="text-sm font-medium">Featured research image</p>
                <p class="mt-1 text-sm text-muted-foreground">This image appears on the Home Featured research cards and thesis listing cards.</p>
                <img v-if="selected.featured_image_path" :src="`/storage/${selected.featured_image_path}`" :alt="selected.title" class="mt-4 h-40 w-full max-w-md rounded-md object-cover" />
                <Form :action="`/admin/theses/${selected.id}/featured-image`" method="post" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input name="featured_image" type="file" accept="image/png,image/jpeg,image/webp" class="rounded-md border bg-background px-3 py-2 text-sm" />
                    <button class="w-fit rounded-md border px-4 py-2 text-sm">Upload image</button>
                </Form>
            </div>
            <Form :action="selected ? `/admin/${resource}/${selected.id}` : `/admin/${resource}`" :method="selected ? 'put' : 'post'" class="mt-4 grid gap-4">
                <textarea name="payload" :value="payload" rows="14" class="w-full border border-white/10 bg-[#111] p-4 font-mono text-sm text-white" />
                <p class="text-sm text-white/50">Enter JSON for the record payload. Relationships use IDs from existing developers, theses, categories, and users.</p>
                <button class="w-fit bg-[#c79b55] px-4 py-2 text-sm text-black">Save record</button>
            </Form>
        </section>

        <div class="flex gap-2">
            <Link v-for="link in items.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="rounded border px-3 py-2 text-sm" :class="{ 'bg-primary text-primary-foreground': link.active, 'pointer-events-none opacity-40': !link.url }" />
        </div>
    </div>
</template>
