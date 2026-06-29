<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

defineProps<{ developers: any }>();
</script>

<template>
    <PublicLayout title="Developers">
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader eyebrow="Developer directory" title="Public certification visibility." body="Developer profiles show status, evaluation summary, and related positions." />
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <EntityCard v-for="developer in developers.data" :key="developer.id" :href="`/developers/${developer.slug}`" :title="developer.name" :body="developer.summary" :eyebrow="developer.certification_status" :meta="`${developer.positions_count} positions / score ${developer.rating_score}`" />
            </div>
        </section>
    </PublicLayout>
</template>
