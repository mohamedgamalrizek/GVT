<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

const sections = ['Market research', 'Developer vetting', 'Asset analysis', 'Currency dynamics', 'Portfolio positioning', 'Risk context', 'Investment rationale'];
</script>

<template>
    <PublicLayout title="Methodology">
        <section class="mx-auto max-w-7xl px-5 py-24 lg:px-8">
            <SectionHeader eyebrow="Methodology" title="Evidence before exposure." body="Our process is structured to make the investment rationale legible, comparable, and accountable." />
            <div class="mt-12 grid gap-4">
                <div v-for="(section, index) in sections" :key="section" class="grid gap-6 border border-white/10 p-6 md:grid-cols-[120px_1fr]">
                    <p class="text-[#f2cf74]">0{{ index + 1 }}</p>
                    <div>
                        <h2 class="text-2xl font-semibold">{{ section }}</h2>
                        <p class="mt-3 max-w-3xl text-white/62">We examine documented evidence, comparable transactions, delivery pathways, downside controls, and the specific role a position can play inside a portfolio.</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
