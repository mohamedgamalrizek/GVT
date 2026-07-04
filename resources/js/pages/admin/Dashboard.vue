<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    stats: Record<string, number>;
    messages: any[];
    positions: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin', href: '/admin' }],
    },
});

const page = usePage();
const permissions = computed(() => page.props.auth.permissions ?? []);

const resources = computed(() => [
    ['CRM', 'crm', 'manage assigned clients'],
    ['Pages', 'pages', 'manage pages'],
    ['Theses', 'theses', 'manage theses'],
    ['Positions', 'positions', 'manage positions'],
    ['Developers', 'developers', 'manage developers'],
    ['Certifications', 'certifications', 'manage certifications'],
    ['Blog', 'blog', 'manage blog'],
    ['Contact', 'contact-messages', 'view inquiries'],
    ['Users', 'users', 'manage users'],
    ['Roles', 'roles', 'manage roles'],
    ['Settings', 'settings', 'manage settings'],
].filter((resource) => permissions.value.includes(resource[2])));
</script>

<template>
    <Head title="Admin" />
    <div class="grid min-h-full gap-6 bg-[#050505] p-4 text-white md:p-6">
        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Global Value Thesis</p>
            <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.08em]">Institutional command center</h1>
        </section>
        <section class="grid gap-4 md:grid-cols-4">
            <div v-for="(value, key) in stats" :key="key" class="border border-[#1a1712] bg-[#0b0b0b] p-5">
                <p class="text-sm capitalize text-white/50">{{ String(key).replace(/([A-Z])/g, ' $1') }}</p>
                <p class="mt-2 text-3xl font-semibold text-[#c79b55]">{{ value }}</p>
            </div>
        </section>
        <section class="grid gap-4 md:grid-cols-3">
            <Link v-for="[label, resource] in resources" :key="resource" :href="`/admin/${resource}`" class="border border-[#1a1712] bg-[#0b0b0b] p-5 transition hover:border-[#c79b55]">
                <p class="font-semibold">{{ label }}</p>
                <p class="mt-2 text-sm text-white/50">Manage {{ label.toLowerCase() }} records.</p>
            </Link>
        </section>
        <section class="grid gap-4 md:grid-cols-2">
            <div class="border border-[#1a1712] bg-[#0b0b0b] p-5">
                <h2 class="font-semibold">Recent inquiries</h2>
                <div class="mt-4 grid gap-3">
                    <div v-for="message in messages" :key="message.id" class="border-t border-white/10 pt-3 text-sm">
                        <p class="font-medium">{{ message.subject }}</p>
                        <p class="text-white/50">{{ message.name }} / {{ message.inquiry_type }}</p>
                    </div>
                </div>
            </div>
            <div class="border border-[#1a1712] bg-[#0b0b0b] p-5">
                <h2 class="font-semibold">Recent positions</h2>
                <div class="mt-4 grid gap-3">
                    <div v-for="position in positions" :key="position.id" class="border-t border-white/10 pt-3 text-sm">
                        <p class="font-medium">{{ position.project_name }}</p>
                        <p class="text-white/50">{{ position.developer?.name }} / {{ position.risk_level }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
