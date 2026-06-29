<script setup lang="ts">
defineProps<{
    eyebrow?: string;
    title: string;
    body?: string;
}>();
</script>

<template>
    <div class="max-w-3xl">
        <p v-if="eyebrow" class="mb-3 text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ eyebrow }}</p>
        <h2 class="text-3xl font-semibold tracking-tight text-white md:text-5xl">{{ title }}</h2>
        <p v-if="body" class="mt-5 text-base leading-7 text-white/62 md:text-lg">{{ body }}</p>
    </div>
</template>
