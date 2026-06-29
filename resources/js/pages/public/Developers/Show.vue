<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';

defineProps<{ developer: any }>();
</script>

<template>
    <PublicLayout :title="developer.name">
        <section class="mx-auto max-w-6xl px-5 py-20 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ developer.certification_status }}</p>
            <h1 class="mt-5 text-4xl font-semibold md:text-6xl">{{ developer.name }}</h1>
            <p class="mt-6 max-w-3xl text-xl leading-8 text-white/68">{{ developer.evaluation_summary }}</p>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <div class="border border-white/10 p-5"><p class="text-white/45">Rating score</p><p class="mt-2 text-3xl text-[#f2cf74]">{{ developer.rating_score }}</p></div>
                <div class="border border-white/10 p-5"><p class="text-white/45">Headquarters</p><p class="mt-2 text-xl">{{ developer.headquarters }}</p></div>
                <div class="border border-white/10 p-5"><p class="text-white/45">Founded</p><p class="mt-2 text-xl">{{ developer.founded_year }}</p></div>
            </div>
            <section class="mt-12 border-t border-white/10 pt-8">
                <h2 class="text-2xl font-semibold">Certification history</h2>
                <div class="mt-5 grid gap-3">
                    <div v-for="event in developer.certification_histories" :key="event.id" class="border border-white/10 p-5">
                        <p class="text-[#f2cf74]">{{ event.to_status }} / {{ event.event_type }}</p>
                        <p class="mt-2 text-white/62">{{ event.rationale }}</p>
                    </div>
                </div>
            </section>
            <div class="mt-12 grid gap-4 md:grid-cols-2">
                <EntityCard v-for="position in developer.positions" :key="position.id" :href="`/positions/${position.slug}`" :title="position.project_name" :body="position.thesis_summary" :eyebrow="position.asset_type" :meta="position.risk_level" />
            </div>
        </section>
    </PublicLayout>
</template>
