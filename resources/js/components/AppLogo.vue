<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.siteSettings);
const faviconUrl = computed(() => (settings.value.favicon_path ? `/storage/${settings.value.favicon_path}` : null));
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md border border-[#c79b55]/35 bg-[#0f0f0f] text-[#c79b55]"
    >
        <img v-if="faviconUrl" :src="faviconUrl" :alt="settings.brand_name" class="size-5 object-contain" />
        <AppLogoIcon v-else class="size-5 fill-current text-[#c79b55]" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{ settings.brand_name }}</span>
    </div>
</template>
