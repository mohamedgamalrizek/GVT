<script setup lang="ts">
import EntityCard from '@/components/public/EntityCard.vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';
import { computed } from 'vue';

const props = defineProps<{
    certificationSections: Record<string, {
        eyebrow: string | null;
        heading: string;
        body: string | null;
        content: Record<string, any> | null;
    }>;
    statuses: Record<string, number>;
    developers: any[];
}>();

const hero = computed(() => props.certificationSections.hero ?? {});
const methodology = computed(() => props.certificationSections.methodology ?? {});
const heroImage = computed(() => hero.value.content?.image_path ? `/storage/${hero.value.content.image_path}` : null);
const methodologySteps = computed(() => methodology.value.content?.steps ?? {});

function developerImage(path: string | null) {
    return path ? `/storage/${path}` : undefined;
}
</script>

<template>
    <PublicLayout :title="hero.heading || 'GVT Certification Portal'" :description="hero.body || undefined">
        <section class="relative overflow-hidden border-b border-white/10 px-5 py-20 lg:px-8">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_10%,rgba(199,155,85,0.18),transparent_34%),linear-gradient(135deg,#050505_0%,#11100d_52%,#050505_100%)]" />
            <div class="relative mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr_0.85fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ hero.eyebrow || 'Certification portal' }}</p>
                    <h1 class="mt-6 max-w-4xl text-5xl font-light uppercase leading-[0.95] tracking-[0.04em] text-white md:text-7xl">
                        {{ hero.heading || 'Transparent developer vetting.' }}
                    </h1>
                    <p class="mt-8 max-w-2xl text-lg leading-8 text-white/65">
                        {{ hero.body || 'If we cannot certify the developer, we do not present the asset.' }}
                    </p>
                </div>
                <div class="overflow-hidden border border-white/10 bg-[#0b0b0b]">
                    <img v-if="heroImage" :src="heroImage" :alt="hero.heading || 'Certification portal'" class="aspect-[4/3] w-full object-cover" />
                    <div v-else class="grid aspect-[4/3] place-items-center bg-[linear-gradient(135deg,#111,#050505)] p-8">
                        <div class="text-center">
                            <p class="text-xs uppercase tracking-[0.28em] text-[#f2cf74]">GVT Certification</p>
                            <p class="mt-4 text-3xl font-light uppercase tracking-[0.08em] text-white">Evidence before exposure</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="mt-10 grid gap-4 md:grid-cols-4">
                <div v-for="status in ['certified', 'under_review', 'watchlist', 'revoked']" :key="status" class="border border-white/10 p-6">
                    <p class="text-sm uppercase tracking-[0.2em] text-white/45">{{ status }}</p>
                    <p class="mt-4 text-4xl text-[#f2cf74]">{{ statuses[status] || 0 }}</p>
                </div>
            </div>
            <div class="mt-12 grid gap-4 md:grid-cols-3">
                <EntityCard
                    v-for="developer in developers"
                    :key="developer.id"
                    :href="`/certification/developers/${developer.slug}`"
                    :title="developer.name"
                    :body="developer.evaluation_summary || developer.summary"
                    :eyebrow="developer.certification_status"
                    :meta="`Score ${developer.rating_score}`"
                    :image="developerImage(developer.logo_path)"
                />
            </div>
        </section>

        <section class="border-y border-white/10 bg-[#090909] px-5 py-20 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr]">
                <SectionHeader
                    :eyebrow="methodology.eyebrow || 'Certification methodology'"
                    :title="methodology.heading || 'Evidence before exposure.'"
                    :body="methodology.body || 'Developer certification is reviewed through governance, delivery record, financial resilience, legal clarity, buyer protection, and operating partner quality.'"
                />
                <div class="grid gap-4 md:grid-cols-2">
                    <article v-for="(body, title) in methodologySteps" :key="title" class="border border-white/10 bg-[#0f0f0f] p-6">
                        <h2 class="text-lg font-semibold text-white">{{ title }}</h2>
                        <p class="mt-3 leading-7 text-white/58">{{ body }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
