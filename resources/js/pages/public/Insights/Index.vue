<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';

defineProps<{ posts: any; categories: any[] }>();

function imageUrl(path: string | null) {
    return path ? `/storage/${path}` : undefined;
}
</script>

<template>
    <PublicLayout title="Insights">
        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader eyebrow="Insights" title="Research notes for cycle-aware investors." />
            <div class="mt-8 flex flex-wrap gap-2">
                <a v-for="category in categories" :key="category.slug" :href="`/insights?category=${category.slug}`" class="border border-white/10 px-3 py-2 text-sm text-white/65">{{ category.name }}</a>
            </div>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <EntityCard
                    v-for="post in posts.data"
                    :key="post.id"
                    :href="`/insights/${post.slug}`"
                    :title="post.title"
                    :body="post.excerpt"
                    :eyebrow="post.category?.name"
                    :meta="post.published_at"
                    :image="imageUrl(post.featured_image_path)"
                />
            </div>
        </section>
    </PublicLayout>
</template>
