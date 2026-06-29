<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

defineProps<{ statuses: Record<string, number>; developers: any[] }>();
</script>

<template>
    <PublicLayout title="GVT Certification Portal">
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader eyebrow="Certification portal" title="Transparent developer vetting." body="If we cannot certify the developer, we do not present the asset. Status changes and watchlist entries remain publicly visible." />
            <div class="mt-10 grid gap-4 md:grid-cols-4">
                <div v-for="status in ['certified', 'under_review', 'watchlist', 'revoked']" :key="status" class="border border-white/10 p-6">
                    <p class="text-sm uppercase tracking-[0.2em] text-white/45">{{ status }}</p>
                    <p class="mt-4 text-4xl text-[#f2cf74]">{{ statuses[status] || 0 }}</p>
                </div>
            </div>
            <div class="mt-12 grid gap-4 md:grid-cols-3">
                <EntityCard v-for="developer in developers" :key="developer.id" :href="`/certification/developers/${developer.slug}`" :title="developer.name" :body="developer.evaluation_summary || developer.summary" :eyebrow="developer.certification_status" :meta="`Score ${developer.rating_score}`" />
            </div>
        </section>
    </PublicLayout>
</template>
