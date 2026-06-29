<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

defineProps<{ theses: any; filters: Record<string, string>; filterOptions: any }>();
</script>

<template>
    <PublicLayout title="Investment Theses">
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader eyebrow="Research library" title="Investment theses." body="Filter by market, city, category, and status to compare positioning windows." />
            <form class="mt-10 grid gap-3 md:grid-cols-5" method="get">
                <input name="search" :value="filters.search" placeholder="Search thesis" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white" />
                <select v-for="field in ['market', 'city', 'category', 'status']" :key="field" :name="field" class="border border-white/10 bg-[#080808] px-4 py-3 text-sm text-white">
                    <option value="">{{ field }}</option>
                    <option v-for="option in filterOptions[field === 'market' ? 'markets' : field === 'city' ? 'cities' : field === 'category' ? 'categories' : 'statuses']" :key="option" :value="option" :selected="filters[field] === option">{{ option }}</option>
                </select>
                <button class="bg-[#f2cf74] px-4 py-3 text-sm font-semibold text-black">Apply</button>
            </form>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <EntityCard v-for="thesis in theses.data" :key="thesis.id" :href="`/theses/${thesis.slug}`" :title="thesis.title" :body="thesis.executive_summary" :eyebrow="`${thesis.market} / ${thesis.city}`" :meta="thesis.positioning_window" :image="thesis.featured_image_path ? `/storage/${thesis.featured_image_path}` : null" />
            </div>
        </section>
    </PublicLayout>
</template>
