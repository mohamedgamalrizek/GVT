<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

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

const resources = [
    ['Pages', 'pages'],
    ['Theses', 'theses'],
    ['Positions', 'positions'],
    ['Developers', 'developers'],
    ['Certifications', 'certifications'],
    ['Blog', 'blog'],
    ['Newsletter', 'newsletter'],
    ['Contact Messages', 'contact-messages'],
    ['Users', 'users'],
    ['Roles', 'roles'],
    ['Settings', 'settings'],
];
</script>

<template>
    <Head title="Admin" />
    <div class="grid gap-6 p-4">
        <section class="rounded-lg border bg-card p-6">
            <p class="text-sm text-muted-foreground">Global Value Thesis</p>
            <h1 class="mt-2 text-3xl font-semibold">Institutional command center</h1>
        </section>
        <section class="grid gap-4 md:grid-cols-4">
            <div v-for="(value, key) in stats" :key="key" class="rounded-lg border bg-card p-5">
                <p class="text-sm capitalize text-muted-foreground">{{ String(key).replace(/([A-Z])/g, ' $1') }}</p>
                <p class="mt-2 text-3xl font-semibold">{{ value }}</p>
            </div>
        </section>
        <section class="grid gap-4 md:grid-cols-3">
            <Link v-for="[label, resource] in resources" :key="resource" :href="`/admin/${resource}`" class="rounded-lg border bg-card p-5 transition hover:border-primary">
                <p class="font-semibold">{{ label }}</p>
                <p class="mt-2 text-sm text-muted-foreground">Manage {{ label.toLowerCase() }} records.</p>
            </Link>
        </section>
        <section class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border bg-card p-5">
                <h2 class="font-semibold">Recent inquiries</h2>
                <div class="mt-4 grid gap-3">
                    <div v-for="message in messages" :key="message.id" class="border-t pt-3 text-sm">
                        <p class="font-medium">{{ message.subject }}</p>
                        <p class="text-muted-foreground">{{ message.name }} / {{ message.inquiry_type }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border bg-card p-5">
                <h2 class="font-semibold">Recent positions</h2>
                <div class="mt-4 grid gap-3">
                    <div v-for="position in positions" :key="position.id" class="border-t pt-3 text-sm">
                        <p class="font-medium">{{ position.project_name }}</p>
                        <p class="text-muted-foreground">{{ position.developer?.name }} / {{ position.risk_level }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
