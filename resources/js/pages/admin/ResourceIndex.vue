<script setup lang="ts">
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Field = {
    name: string;
    label: string;
    type: string;
    source?: string | null;
    options?: Array<{ value: string; label: string }>;
    required?: boolean;
    help?: string | null;
};

const props = defineProps<{
    resource: string;
    title: string;
    items: any;
    meta: Record<string, any[]>;
    fields: Field[];
    tableFields: string[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin', href: '/admin' }],
    },
});

const selected = ref<any | null>(null);
const form = useForm<{ payload: Record<string, any>; featured_image: File | null }>({ payload: {}, featured_image: null });

const rows = computed(() => props.items.data ?? []);

function createNew() {
    selected.value = null;
    form.clearErrors();
    form.payload = {};
    form.featured_image = null;

    for (const field of props.fields) {
        if (field.type === 'multiRelation' || field.type === 'list') {
            form.payload[field.name] = [];
        } else if (field.type === 'keyValue') {
            form.payload[field.name] = {};
        } else if (field.type === 'image') {
            form.payload[field.name] = null;
        } else if (field.options?.length) {
            form.payload[field.name] = field.options[0]?.value ?? '';
        } else {
            form.payload[field.name] = '';
        }
    }
}

function edit(item: any) {
    selected.value = item;
    form.clearErrors();
    form.payload = {};
    form.featured_image = null;

    for (const field of props.fields) {
        if (field.name === 'tag_ids') {
            form.payload[field.name] = item.tags?.map((tag: any) => tag.id) ?? [];
        } else if (field.name === 'role_id') {
            form.payload[field.name] = item.roles?.[0]?.id ?? '';
        } else if (field.name === 'permission_ids') {
            form.payload[field.name] = item.permissions?.map((permission: any) => permission.id) ?? [];
        } else if (field.type === 'datetime') {
            form.payload[field.name] = dateTimeInput(item[field.name]);
        } else if (field.type === 'date') {
            form.payload[field.name] = dateInput(item[field.name]);
        } else if (field.type === 'list') {
            form.payload[field.name] = normalizeList(item[field.name]);
        } else if (field.type === 'keyValue') {
            form.payload[field.name] = normalizeObject(item[field.name]);
        } else if (field.type === 'password') {
            form.payload[field.name] = '';
        } else if (field.type === 'image') {
            form.payload[field.name] = item[field.name] ?? '';
        } else {
            form.payload[field.name] = item[field.name] ?? '';
        }
    }
}

function submit() {
    if (selected.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/admin/${props.resource}/${selected.value.id}`, { preserveScroll: true, forceFormData: true });
    } else {
        form.transform((data) => data).post(`/admin/${props.resource}`, { preserveScroll: true, forceFormData: true });
    }
}

function destroy(item: any) {
    if (!window.confirm('Delete this record?')) {
        return;
    }

    useForm({}).delete(`/admin/${props.resource}/${item.id}`, { preserveScroll: true });
}

function relationOptions(field: Field) {
    return props.meta[field.source ?? ''] ?? [];
}

function optionName(option: any) {
    return option.name ?? option.title ?? option.email ?? `#${option.id}`;
}

function displayValue(item: any, key: string) {
    if (key === 'developer') {
        return item.developer?.name ?? 'None';
    }

    if (key === 'category') {
        return item.category?.name ?? 'None';
    }

    if (key === 'roles') {
        return item.roles?.map((role: any) => role.name).join(', ') || 'None';
    }

    if (key === 'permissions') {
        return item.permissions?.map((permission: any) => permission.name).join(', ') || 'None';
    }

    const value = item[key];

    if (Array.isArray(value)) {
        return value.join(', ');
    }

    if (typeof value === 'object' && value !== null) {
        return Object.values(value).join(', ');
    }

    return value ?? 'None';
}

function normalizeList(value: any) {
    if (Array.isArray(value)) {
        return value.map((item) => (typeof item === 'object' ? item.name || item.path || JSON.stringify(item) : String(item)));
    }

    if (typeof value === 'string' && value.length > 0) {
        return value.split('\n');
    }

    return [];
}

function normalizeObject(value: any) {
    if (value && typeof value === 'object' && !Array.isArray(value)) {
        return value;
    }

    return {};
}

function listText(field: Field) {
    return normalizeList(form.payload[field.name]).join('\n');
}

function updateList(field: Field, event: Event) {
    form.payload[field.name] = (event.target as HTMLTextAreaElement).value
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);
}

function keyValueText(field: Field) {
    return Object.entries(normalizeObject(form.payload[field.name]))
        .map(([key, value]) => `${key}: ${value}`)
        .join('\n');
}

function updateKeyValue(field: Field, event: Event) {
    const rows = (event.target as HTMLTextAreaElement).value
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);

    form.payload[field.name] = Object.fromEntries(rows.map((row) => {
        const [key, ...value] = row.split(':');

        return [key.trim(), value.join(':').trim()];
    }).filter(([key]) => key));
}

function updateMulti(field: Field, event: Event) {
    form.payload[field.name] = Array.from((event.target as HTMLSelectElement).selectedOptions).map((option) => Number(option.value));
}

function updateImage(event: Event) {
    form.featured_image = (event.target as HTMLInputElement).files?.[0] ?? null;
}

function imagePreviewPath() {
    return selected.value?.featured_image_path ?? selected.value?.logo_path ?? null;
}

function dateTimeInput(value: string | null) {
    return value ? value.slice(0, 16) : '';
}

function dateInput(value: string | null) {
    return value ? value.slice(0, 10) : '';
}

createNew();
</script>

<template>
    <Head :title="title" />
    <div class="grid min-h-full gap-6 bg-[#050505] p-4 text-white md:p-6">
        <section class="flex flex-col justify-between gap-4 border border-[#1a1712] bg-[#0b0b0b] p-6 md:flex-row md:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Admin resource</p>
                <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.08em]">{{ title }}</h1>
                <p class="mt-3 text-sm text-white/50">Add, edit, and delete records using simple fields. No JSON editing required.</p>
            </div>
            <button class="border border-[#c79b55] px-4 py-2 text-sm text-[#c79b55]" type="button" @click="createNew">New record</button>
        </section>

        <section class="overflow-hidden border border-[#1a1712] bg-[#0b0b0b]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#111] text-white/50">
                        <tr>
                            <th v-for="key in tableFields" :key="key" class="px-4 py-3 font-medium capitalize">{{ key.replaceAll('_', ' ') }}</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in rows" :key="item.id" class="border-t border-white/10">
                            <td v-for="key in tableFields" :key="key" class="max-w-72 truncate px-4 py-3">{{ displayValue(item, key) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <button class="text-[#c79b55] underline" type="button" @click="edit(item)">Edit</button>
                                    <button class="text-red-300 underline" type="button" @click="destroy(item)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <h2 class="text-xl font-semibold">{{ selected ? 'Edit record' : 'Create record' }}</h2>

            <div v-if="resource === 'theses' && selected" class="mt-4 border border-white/10 bg-[#111] p-4">
                <p class="text-sm font-medium">Featured research image</p>
                <p class="mt-1 text-sm text-white/45">This image appears on the Home Featured research cards and thesis listing cards.</p>
                <img v-if="selected.featured_image_path" :src="`/storage/${selected.featured_image_path}`" :alt="selected.title" class="mt-4 h-40 w-full max-w-md object-cover" />
                <Form :action="`/admin/theses/${selected.id}/featured-image`" method="post" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input name="featured_image" type="file" accept="image/png,image/jpeg,image/webp" class="border border-white/10 bg-[#0b0b0b] px-3 py-2 text-sm" />
                    <button class="w-fit border border-[#c79b55] px-4 py-2 text-sm text-[#c79b55]">Upload image</button>
                </Form>
            </div>

            <form class="mt-6 grid gap-5 md:grid-cols-2" @submit.prevent="submit">
                <label v-for="field in fields" :key="field.name" class="grid gap-2 text-sm" :class="{ 'md:col-span-2': ['textarea', 'list', 'keyValue', 'multiRelation'].includes(field.type) }">
                    <span class="text-white/60">{{ field.label }} <span v-if="field.required" class="text-[#c79b55]">*</span></span>

                    <textarea
                        v-if="field.type === 'textarea'"
                        v-model="form.payload[field.name]"
                        rows="5"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                    />

                    <textarea
                        v-else-if="field.type === 'list'"
                        :value="listText(field)"
                        rows="5"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                        @input="updateList(field, $event)"
                    />

                    <textarea
                        v-else-if="field.type === 'keyValue'"
                        :value="keyValueText(field)"
                        rows="5"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                        @input="updateKeyValue(field, $event)"
                    />

                    <select
                        v-else-if="field.type === 'select'"
                        v-model="form.payload[field.name]"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                    >
                        <option v-for="option in field.options" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>

                    <select
                        v-else-if="field.type === 'relation'"
                        v-model="form.payload[field.name]"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                    >
                        <option value="">None</option>
                        <option v-for="option in relationOptions(field)" :key="option.id" :value="option.id">{{ optionName(option) }}</option>
                    </select>

                    <select
                        v-else-if="field.type === 'multiRelation'"
                        :value="form.payload[field.name]"
                        multiple
                        size="7"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                        @change="updateMulti(field, $event)"
                    >
                        <option v-for="option in relationOptions(field)" :key="option.id" :value="option.id">{{ optionName(option) }}</option>
                    </select>

                    <div v-else-if="field.type === 'image'" class="grid gap-3">
                        <img v-if="imagePreviewPath()" :src="`/storage/${imagePreviewPath()}`" :alt="selected?.title || selected?.name || field.label" class="h-44 w-full max-w-lg border border-white/10 object-cover" />
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                            @change="updateImage"
                        />
                    </div>

                    <input
                        v-else
                        v-model="form.payload[field.name]"
                        :type="field.type === 'datetime' ? 'datetime-local' : field.type"
                        class="border border-white/10 bg-[#111] px-4 py-3 text-white outline-none focus:border-[#c79b55]"
                    />

                    <span v-if="field.help" class="text-xs text-white/40">{{ field.help }}</span>
                    <span v-if="form.errors[`payload.${field.name}`]" class="text-xs text-[#c79b55]">{{ form.errors[`payload.${field.name}`] }}</span>
                </label>

                <div class="md:col-span-2 flex items-center gap-4">
                    <button :disabled="form.processing" class="w-fit bg-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black disabled:opacity-60">
                        {{ selected ? 'Save changes' : 'Create record' }}
                    </button>
                    <span v-if="form.recentlySuccessful" class="text-sm text-[#c79b55]">Saved.</span>
                </div>
            </form>
        </section>

        <div class="flex flex-wrap gap-2">
            <Link v-for="link in items.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="border border-white/10 px-3 py-2 text-sm" :class="{ 'bg-[#c79b55] text-black': link.active, 'pointer-events-none opacity-40': !link.url }" />
        </div>
    </div>
</template>
