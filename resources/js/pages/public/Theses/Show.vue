<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';

defineProps<{ thesis: any }>();
</script>

<template>
    <PublicLayout :title="thesis.seo_title || thesis.title" :description="thesis.seo_description">
        <article class="mx-auto max-w-5xl px-5 py-20 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ thesis.market }} / {{ thesis.city }}</p>
            <h1 class="mt-5 text-4xl font-semibold md:text-6xl">{{ thesis.title }}</h1>
            <p class="mt-6 text-xl leading-8 text-white/68">{{ thesis.executive_summary }}</p>
            <div class="mt-12 grid gap-8">
                <section v-for="[label, body] in [['Market context', thesis.market_context], ['Asset rationale', thesis.asset_rationale], ['Risk notes', thesis.risk_notes], ['Positioning window', thesis.positioning_window]]" :key="label" class="border-t border-white/10 pt-8">
                    <h2 class="text-2xl font-semibold">{{ label }}</h2>
                    <p class="mt-4 whitespace-pre-line leading-7 text-white/62">{{ body }}</p>
                </section>
            </div>
            <div v-if="thesis.positions?.length" class="mt-12 grid gap-4 md:grid-cols-2">
                <EntityCard v-for="position in thesis.positions" :key="position.id" :href="`/positions/${position.slug}`" :title="position.project_name" :body="position.thesis_summary" :eyebrow="position.asset_type" :meta="position.risk_level" />
            </div>
        </article>
    </PublicLayout>
</template>
