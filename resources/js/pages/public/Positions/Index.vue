<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

defineProps<{ positions: any; filters: Record<string, string>; filterOptions: any }>();
</script>

<template>
    <PublicLayout title="Positions">
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader eyebrow="Positions" title="Not listings. Positions." body="Each card summarizes the investment rationale, developer certification, and risk posture." />
            <form class="mt-10 grid gap-3 md:grid-cols-5" method="get">
                <input name="search" :value="filters.search" placeholder="Search position" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-white" />
                <select v-for="field in ['city', 'asset_type', 'risk_level', 'certification_status']" :key="field" :name="field" class="border border-white/10 bg-[#080808] px-4 py-3 text-sm text-white">
                    <option value="">{{ field }}</option>
                    <option v-for="option in filterOptions[field === 'asset_type' ? 'assetTypes' : field === 'risk_level' ? 'riskLevels' : field === 'certification_status' ? 'certificationStatuses' : 'cities']" :key="option" :value="option" :selected="filters[field] === option">{{ option }}</option>
                </select>
                <button class="bg-[#f2cf74] px-4 py-3 text-sm font-semibold text-black">Apply</button>
            </form>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <EntityCard v-for="position in positions.data" :key="position.id" :href="`/positions/${position.slug}`" :title="position.project_name" :body="position.thesis_summary" :eyebrow="`${position.asset_type} / ${position.city}`" :meta="`${position.risk_level} risk / ${position.certification_status}`" />
            </div>
        </section>
    </PublicLayout>
</template>
