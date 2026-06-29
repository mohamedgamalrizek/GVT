<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';
</script>

<template>
    <PublicLayout title="About GVT">
        <section class="mx-auto max-w-7xl px-5 py-24 lg:px-8">
            <SectionHeader eyebrow="About" title="Investment-first real estate advisory." body="GVT exists for investors who require context before exposure. The company thinks in cycles, capital flows, governance quality, and structural value." />
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <div v-for="item in ['We do not treat assets as launches.', 'We do not separate upside from risk.', 'We do not present developers without evidence.']" :key="item" class="border border-white/10 p-7 text-xl leading-8">{{ item }}</div>
            </div>
        </section>
    </PublicLayout>
</template>
