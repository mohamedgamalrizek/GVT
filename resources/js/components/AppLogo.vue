<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.siteSettings);
const logoUrl = computed(() => (settings.value.logo_path ? `/storage/${settings.value.logo_path}` : null));
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-[#c79b55] text-black"
    >
        <img v-if="logoUrl" :src="logoUrl" :alt="settings.brand_name" class="size-6 object-contain" />
        <AppLogoIcon v-else class="size-5 fill-current text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{ settings.brand_name }}</span>
    </div>
</template>
