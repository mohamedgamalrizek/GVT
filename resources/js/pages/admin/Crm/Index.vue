<script setup lang="ts">
import { Head, Form, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type Option = {
    value: string;
    label: string;
};

type UserSummary = {
    id: number;
    name: string;
    email: string;
};

type ClientUpdate = {
    id: number;
    type: string;
    status_from: string | null;
    status_to: string | null;
    body: string;
    contacted_at: string | null;
    next_follow_up_at: string | null;
    created_at: string;
    user: UserSummary | null;
};

type Client = {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    company: string | null;
    investor_type: string | null;
    status: string;
    priority: string;
    source: string | null;
    budget_range: string | null;
    preferred_market: string | null;
    notes: string | null;
    assigned_to_user_id: number | null;
    created_by_user_id: number | null;
    last_contacted_at: string | null;
    next_follow_up_at: string | null;
    updated_at: string;
    assigned_to: UserSummary | null;
    created_by: UserSummary | null;
    updates?: ClientUpdate[];
};

const props = defineProps<{
    clients: {
        data: Client[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    selectedClient: Client | null;
    salesUsers: UserSummary[];
    filters: {
        search?: string;
        status?: string;
        priority?: string;
    };
    statusOptions: Option[];
    priorityOptions: Option[];
    canManageCrm: boolean;
    stats: {
        total: number;
        open: number;
        won: number;
        followUps: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'CRM', href: '/admin/crm' }],
    },
});

const defaultAssignee = computed(() => props.salesUsers[0]?.id ?? '');

function labelFor(options: Option[], value: string | null) {
    return options.find((option) => option.value === value)?.label ?? value ?? 'Unassigned';
}

function dateInput(value: string | null) {
    return value ? value.slice(0, 16) : '';
}

function badgeClass(value: string) {
    const map: Record<string, string> = {
        urgent: 'border-red-400/40 bg-red-400/10 text-red-200',
        high: 'border-[#c79b55]/50 bg-[#c79b55]/12 text-[#f2cf74]',
        won: 'border-emerald-400/40 bg-emerald-400/10 text-emerald-200',
        lost: 'border-white/15 bg-white/5 text-white/45',
    };

    return map[value] ?? 'border-white/12 bg-white/[0.04] text-white/65';
}
</script>

<template>
    <Head title="CRM" />

    <div class="min-h-full bg-[#050505] p-4 text-white md:p-6">
        <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Client relationship management</p>
            <div class="mt-3 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                <div>
                    <h1 class="text-3xl font-light uppercase tracking-[0.08em]">CRM</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-white/55">Assign investors to sales, track status, record updates, and keep client visibility scoped by ownership.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div v-for="item in [
                        { label: 'Total', value: stats.total },
                        { label: 'Open', value: stats.open },
                        { label: 'Won', value: stats.won },
                        { label: 'Follow-ups', value: stats.followUps },
                    ]" :key="item.label" class="border border-white/10 bg-[#111] px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.16em] text-white/40">{{ item.label }}</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ item.value }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 grid gap-6 xl:grid-cols-[420px_1fr]">
            <div class="grid gap-6">
                <section class="border border-[#1a1712] bg-[#0b0b0b] p-5">
                    <Form action="/admin/crm" method="get" class="grid gap-3">
                        <input name="search" :value="filters.search" placeholder="Search name, phone, email, company" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <div class="grid grid-cols-2 gap-3">
                            <select name="status" :value="filters.status" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                                <option value="">All statuses</option>
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                            </select>
                            <select name="priority" :value="filters.priority" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                                <option value="">All priorities</option>
                                <option v-for="option in priorityOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                            </select>
                        </div>
                        <button class="bg-[#c79b55] px-4 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black">Filter clients</button>
                    </Form>
                </section>

                <section class="overflow-hidden border border-[#1a1712] bg-[#0b0b0b]">
                    <div class="border-b border-white/10 px-5 py-4">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Clients</h2>
                    </div>
                    <div class="grid">
                        <Link
                            v-for="client in clients.data"
                            :key="client.id"
                            :href="`/admin/crm/${client.id}`"
                            class="border-b border-white/10 p-5 transition hover:bg-white/[0.03]"
                            :class="selectedClient?.id === client.id ? 'bg-[#c79b55]/10' : ''"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-white">{{ client.name }}</h3>
                                    <p class="mt-1 text-sm text-white/45">{{ client.phone || client.email || 'No contact data' }}</p>
                                </div>
                                <span class="border px-2 py-1 text-[10px] uppercase tracking-[0.14em]" :class="badgeClass(client.priority)">
                                    {{ labelFor(priorityOptions, client.priority) }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs text-white/45">
                                <span class="border border-white/10 px-2 py-1">{{ labelFor(statusOptions, client.status) }}</span>
                                <span class="border border-white/10 px-2 py-1">{{ client.assigned_to?.name || 'Unassigned' }}</span>
                            </div>
                        </Link>
                    </div>
                    <div class="flex flex-wrap gap-2 p-4">
                        <Link v-for="link in clients.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="border border-white/10 px-3 py-2 text-sm" :class="{ 'bg-[#c79b55] text-black': link.active, 'pointer-events-none opacity-40': !link.url }" />
                    </div>
                </section>
            </div>

            <div class="grid gap-6">
                <section class="border border-[#1a1712] bg-[#0b0b0b] p-6">
                    <h2 class="text-xl font-semibold">Add client</h2>
                    <Form action="/admin/crm" method="post" class="mt-5 grid gap-4 md:grid-cols-2" #default="{ errors, processing }">
                        <input name="name" placeholder="Client name" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="phone" placeholder="Phone" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="email" placeholder="Email" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="company" placeholder="Company" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="investor_type" placeholder="Investor type" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="budget_range" placeholder="Budget range" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="source" placeholder="Source" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="preferred_market" placeholder="Preferred market" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <select name="status" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                        </select>
                        <select name="priority" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option v-for="option in priorityOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                        </select>
                        <select v-if="canManageCrm" name="assigned_to_user_id" :value="defaultAssignee" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option value="">Unassigned</option>
                            <option v-for="sales in salesUsers" :key="sales.id" :value="sales.id">{{ sales.name }}</option>
                        </select>
                        <input name="next_follow_up_at" type="datetime-local" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <textarea name="notes" rows="3" placeholder="Notes" class="md:col-span-2 border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <p v-if="Object.keys(errors).length" class="md:col-span-2 text-sm text-[#c79b55]">Check required fields and contact data.</p>
                        <button :disabled="processing" class="w-fit bg-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black disabled:opacity-60">Create client</button>
                    </Form>
                </section>

                <section v-if="selectedClient" class="border border-[#1a1712] bg-[#0b0b0b] p-6">
                    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#c79b55]">Client profile</p>
                            <h2 class="mt-2 text-3xl font-light">{{ selectedClient.name }}</h2>
                            <p class="mt-2 text-sm text-white/50">{{ selectedClient.phone }} <span v-if="selectedClient.email">/ {{ selectedClient.email }}</span></p>
                        </div>
                        <Form v-if="canManageCrm" :action="`/admin/crm/${selectedClient.id}`" method="delete">
                            <button class="border border-red-400/40 px-4 py-2 text-sm text-red-200">Delete</button>
                        </Form>
                    </div>

                    <Form :action="`/admin/crm/${selectedClient.id}`" method="post" class="mt-6 grid gap-4 md:grid-cols-2" #default="{ processing, recentlySuccessful }">
                        <input type="hidden" name="_method" value="put" />
                        <input name="name" :value="selectedClient.name" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="phone" :value="selectedClient.phone" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="email" :value="selectedClient.email" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="company" :value="selectedClient.company" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="investor_type" :value="selectedClient.investor_type" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="budget_range" :value="selectedClient.budget_range" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="source" :value="selectedClient.source" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <input name="preferred_market" :value="selectedClient.preferred_market" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <select name="status" :value="selectedClient.status" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                        </select>
                        <select name="priority" :value="selectedClient.priority" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option v-for="option in priorityOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                        </select>
                        <select v-if="canManageCrm" name="assigned_to_user_id" :value="selectedClient.assigned_to_user_id || ''" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                            <option value="">Unassigned</option>
                            <option v-for="sales in salesUsers" :key="sales.id" :value="sales.id">{{ sales.name }}</option>
                        </select>
                        <input name="next_follow_up_at" type="datetime-local" :value="dateInput(selectedClient.next_follow_up_at)" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <textarea name="notes" :value="selectedClient.notes" rows="4" class="md:col-span-2 border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                        <div class="md:col-span-2 flex items-center gap-4">
                            <button :disabled="processing" class="w-fit bg-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-black disabled:opacity-60">Save profile</button>
                            <span v-if="recentlySuccessful" class="text-sm text-[#c79b55]">Saved.</span>
                        </div>
                    </Form>
                </section>

                <section v-if="selectedClient" class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="border border-[#1a1712] bg-[#0b0b0b] p-6">
                        <h2 class="text-xl font-semibold">Add update</h2>
                        <Form :action="`/admin/crm/${selectedClient.id}/updates`" method="post" class="mt-5 grid gap-4">
                            <select name="type" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                                <option value="note">Note</option>
                                <option value="call">Call</option>
                                <option value="email">Email</option>
                                <option value="meeting">Meeting</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                            <select name="status_to" :value="selectedClient.status" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]">
                                <option value="">Keep status</option>
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                            </select>
                            <input name="contacted_at" type="datetime-local" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                            <input name="next_follow_up_at" type="datetime-local" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                            <textarea name="body" rows="5" placeholder="What happened? What is the next step?" class="border border-white/10 bg-[#111] px-4 py-3 text-sm text-white outline-none focus:border-[#c79b55]" />
                            <button class="w-fit border border-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55]">Add update</button>
                        </Form>
                    </div>

                    <div class="border border-[#1a1712] bg-[#0b0b0b] p-6">
                        <h2 class="text-xl font-semibold">Timeline</h2>
                        <div class="mt-5 grid gap-4">
                            <article v-for="update in selectedClient.updates || []" :key="update.id" class="border border-white/10 bg-[#111] p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ update.type }}</p>
                                    <p class="text-xs text-white/35">{{ update.created_at?.slice(0, 10) }}</p>
                                </div>
                                <p class="mt-3 leading-7 text-white/70">{{ update.body }}</p>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs text-white/42">
                                    <span>{{ update.user?.name || 'System' }}</span>
                                    <span v-if="update.status_to">Status: {{ labelFor(statusOptions, update.status_to) }}</span>
                                    <span v-if="update.next_follow_up_at">Next: {{ update.next_follow_up_at.slice(0, 10) }}</span>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section v-else class="grid min-h-72 place-items-center border border-[#1a1712] bg-[#0b0b0b] p-6 text-center text-white/45">
                    Select a client to view profile, assignment, and timeline.
                </section>
            </div>
        </section>
    </div>
</template>
