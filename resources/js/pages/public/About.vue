<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';
import { computed } from 'vue';

const props = defineProps<{
    aboutSections: Record<
        string,
        {
            eyebrow: string | null;
            heading: string;
            body: string | null;
            content: Record<string, any> | null;
        }
    >;
}>();

const hero = computed(() => props.aboutSections.hero ?? {});
const philosophy = computed(() => props.aboutSections.philosophy ?? {});
const principles = computed(() => (philosophy.value.content?.principles as string[] | undefined) ?? []);
const thinking = computed(() => props.aboutSections.thinking ?? {});
const thinkingItems = computed(() => (thinking.value.content?.items as Array<{ title: string; body: string }> | undefined) ?? []);
const thesisLed = computed(() => props.aboutSections.thesis_led ?? {});
</script>

<template>
    <PublicLayout :title="hero.heading || 'About GVT'" :description="hero.body || undefined">
        <section class="relative overflow-hidden border-b border-white/10 px-5 py-24 lg:px-8">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_75%_10%,rgba(199,155,85,0.16),transparent_34%),linear-gradient(135deg,#050505_0%,#10100e_52%,#050505_100%)]" />
            <div class="relative mx-auto max-w-7xl">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ hero.eyebrow || 'About Us' }}</p>
                <h1 class="mt-6 max-w-4xl text-5xl font-light uppercase leading-[0.95] tracking-[0.04em] text-white md:text-7xl">
                    {{ hero.heading || 'Investment-first real estate advisory.' }}
                </h1>
                <p class="mt-8 max-w-2xl text-lg leading-8 text-white/65">
                    {{ hero.body || 'GVT exists for investors who require context before exposure.' }}
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <SectionHeader
                :eyebrow="philosophy.eyebrow || 'Brand philosophy'"
                :title="philosophy.heading || 'Context before exposure.'"
                :body="philosophy.body || 'The company thinks in cycles, capital flows, governance quality, and structural value.'"
            />
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <div v-for="item in principles" :key="item" class="border border-white/10 bg-white/[0.03] p-7 text-xl leading-8 text-white">
                    {{ item }}
                </div>
            </div>
        </section>

        <section class="border-y border-white/10 bg-[#090909] px-5 py-20 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr]">
                <SectionHeader
                    :eyebrow="thinking.eyebrow || 'How we think'"
                    :title="thinking.heading || 'A research house before an opportunity desk.'"
                    :body="thinking.body || 'We frame assets through evidence, risk, developer quality, currency context, and portfolio fit.'"
                />
                <div class="grid gap-4">
                    <article v-for="item in thinkingItems" :key="item.title" class="border border-white/10 bg-[#0f0f0f] p-6">
                        <h2 class="text-lg font-semibold text-white">{{ item.title }}</h2>
                        <p class="mt-3 leading-7 text-white/58">{{ item.body }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="grid gap-8 border border-[#c79b55]/35 bg-[#c79b55]/8 p-8 lg:grid-cols-[1fr_1.4fr] lg:p-10">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ thesisLed.eyebrow || 'Why thesis-led matters' }}</p>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-white md:text-5xl">{{ thesisLed.heading || 'Every allocation should explain itself.' }}</h2>
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-white/65">
                        {{ thesisLed.body || 'A thesis creates discipline before capital moves: what must be true, what can go wrong, and where the position belongs in a broader portfolio.' }}
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
