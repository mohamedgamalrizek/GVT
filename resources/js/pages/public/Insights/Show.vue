<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';

defineProps<{ post: any }>();

function imageUrl(path: string | null) {
    return path ? `/storage/${path}` : undefined;
}
</script>

<template>
    <PublicLayout :title="post.seo_title || post.title" :description="post.seo_description">
        <article class="mx-auto max-w-4xl px-5 py-20 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ post.category?.name }}</p>
            <h1 class="mt-5 text-4xl font-semibold md:text-6xl">{{ post.title }}</h1>
            <p class="mt-6 text-xl leading-8 text-white/68">{{ post.excerpt }}</p>
            <div v-if="post.featured_image_path" class="mt-10 overflow-hidden border border-white/10">
                <img :src="imageUrl(post.featured_image_path)" :alt="post.title" class="aspect-[16/9] w-full object-cover" />
            </div>
            <div class="mt-12 border-t border-white/10 pt-8 text-lg leading-8 text-white/70">{{ post.body }}</div>
        </article>
    </PublicLayout>
</template>
