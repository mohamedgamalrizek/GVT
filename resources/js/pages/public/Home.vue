<script setup lang="ts">
import PublicHeader from '@/components/public/PublicHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BarChart3, ChevronLeft, ChevronRight, CircleDollarSign, Compass, Globe2, HelpCircle, Landmark, LineChart, LockKeyhole, Search, ShieldCheck, Target, Users } from '@lucide/vue';
import { computed } from 'vue';

const props = defineProps<{
    homeSections: Record<string, any>;
    featuredTheses: any[];
    featuredDevelopers: any[];
    featuredPositions: any[];
    latestInsights: any[];
}>();

const section = (key: string, fallback: any) => props.homeSections[key] ?? fallback;

const hero = computed(() =>
    section('hero', {
        heading: 'Every investment needs a thesis.',
        body: 'We are a research and advisory house specializing in real estate investments and offshore opportunities.',
        content: { primary_cta: 'Explore Theses', secondary_cta: 'View Positions' },
    }),
);

const approach = computed(() =>
    section('approach', {
        eyebrow: 'Our investment approach',
        heading: 'A structured view on value',
        body: 'Our proprietary framework ensures every investment is backed by rigorous research, real-world data, and deep market knowledge.',
        content: {
            steps: {
                Research: 'We identify structural shifts and emerging opportunities before the market.',
                Challenge: 'We question assumptions and validate every opportunity.',
                Reprice: 'We uncover mispriced assets and hidden value.',
                Position: 'We build conviction-backed positions for long-term capital growth.',
            },
        },
    }),
);

const market = computed(() =>
    section('market_intelligence', {
        eyebrow: 'Market intelligence',
        heading: 'Real-time signals. Smarter decisions.',
        content: { signals: [] },
    }),
);

const thesesSection = computed(() => section('theses', { eyebrow: 'Investment theses', heading: 'Evidence. Conviction. Opportunity.' }));
const positionsSection = computed(() => section('positions', { eyebrow: 'Curated investment opportunities', heading: 'Premium properties. Global standards.' }));
const founder = computed(() => section('founder', { eyebrow: 'Founder', heading: 'Mohamed Salama', body: 'We do not bring you properties. We bring you positions.', content: { bio: '', stats: [] } }));
const cta = computed(() => section('cta', { heading: 'Let us build your next position', body: 'Connect with our team to discuss tailored investment opportunities.' }));

const approachIcons = [Search, HelpCircle, LineChart, Target];
const signalIcons = [LineChart, BarChart3, Target, Users, CircleDollarSign];
const signalSeries = [
    [18, 22, 20, 28, 24, 34, 31, 42, 39, 47],
    [42, 45, 48, 46, 54, 58, 56, 64, 68, 72],
    [28, 31, 30, 38, 41, 40, 52, 49, 60, 69],
    [34, 42, 51, 46, 58, 63],
    [52, 51, 50, 50, 49, 50, 48, 49, 51, 50],
];
const valueItems = [
    { title: 'Research Driven', body: 'Data, insights, evidence.', icon: Search },
    { title: 'Global Perspective', body: 'Local expertise, global reach.', icon: Globe2 },
    { title: 'Capital Intelligence', body: 'Access to offshore markets.', icon: Landmark },
    { title: 'Integrity First', body: 'Independent. Unbiased. Trusted.', icon: ShieldCheck },
];

function imageUrl(path?: string | null) {
    return path ? `/storage/${path}` : null;
}

function sparklinePoints(series: number[]) {
    const width = 126;
    const height = 48;
    const min = Math.min(...series);
    const max = Math.max(...series);
    const range = Math.max(max - min, 1);

    return series
        .map((value, index) => {
            const x = (index / Math.max(series.length - 1, 1)) * width;
            const y = height - ((value - min) / range) * height;

            return `${x.toFixed(1)},${y.toFixed(1)}`;
        })
        .join(' ');
}
</script>

<template>
    <Head title="Global Value Thesis" />

    <div class="min-h-screen bg-[#050505] text-white">
        <section class="relative min-h-[700px] overflow-hidden border-b border-[#1a1712]">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_72%_18%,rgba(203,158,82,0.18),transparent_28%),linear-gradient(90deg,#050505_0%,#070707_42%,#101010_100%)]" />
            <div class="absolute right-[-12%] top-16 hidden h-[560px] w-[760px] rounded-full border border-[#b99251]/20 bg-[radial-gradient(circle_at_45%_45%,rgba(255,255,255,0.12),transparent_3%,transparent_8%,rgba(255,255,255,0.08)_9%,transparent_10%),radial-gradient(circle_at_60%_38%,rgba(203,158,82,0.45),transparent_1%,transparent_2%),linear-gradient(120deg,rgba(255,255,255,0.12),rgba(255,255,255,0.01)_45%,transparent)] opacity-70 blur-[0.2px] lg:block" />
            <div class="absolute right-0 top-20 hidden h-[500px] w-[720px] bg-[linear-gradient(23deg,transparent_48%,rgba(203,158,82,0.18)_49%,transparent_50%),linear-gradient(132deg,transparent_48%,rgba(255,255,255,0.12)_49%,transparent_50%)] bg-[length:110px_110px] opacity-35 lg:block" />
            <div class="relative">
                <PublicHeader overlay />
            </div>
            <div class="relative mx-auto max-w-7xl px-6">
                <div class="grid min-h-[470px] content-center py-16 lg:w-[54%]">
                    <p class="mb-5 text-xs font-semibold uppercase tracking-[0.28em] text-[#c79b55]">{{ hero.eyebrow || 'Global Value Thesis' }}</p>
                    <h1 class="max-w-3xl text-5xl font-light uppercase leading-[1.03] tracking-[0.03em] text-white md:text-7xl">
                        <span>{{ String(hero.heading).split(' needs ')[0] }}</span>
                        <span class="block text-[#c79b55]">Needs a thesis.</span>
                    </h1>
                    <p class="mt-8 max-w-md text-base leading-7 text-white/72">{{ hero.body }}</p>
                    <div class="mt-10 flex flex-wrap gap-4">
                        <Link href="/theses" class="bg-[#c79b55] px-6 py-4 text-xs font-semibold uppercase tracking-[0.14em] text-black">{{ hero.content?.primary_cta || 'Explore Theses' }} <span class="ml-3">--></span></Link>
                        <Link href="/positions" class="border border-white/24 px-6 py-4 text-xs font-semibold uppercase tracking-[0.14em] text-white">{{ hero.content?.secondary_cta || 'View Positions' }}</Link>
                    </div>
                    <div class="mt-20 flex items-center gap-3 text-[10px] uppercase tracking-[0.18em] text-white/45">
                        <ChevronLeft class="size-4" />
                        <span>Scroll to discover</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-[#1a1712] bg-[#090909]">
            <div class="mx-auto grid max-w-7xl divide-y divide-[#1f1a13] px-6 md:grid-cols-4 md:divide-x md:divide-y-0">
                <div v-for="item in valueItems" :key="item.title" class="flex gap-4 py-6 md:px-6">
                    <component :is="item.icon" class="mt-1 size-7 text-[#c79b55]" />
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ item.title }}</p>
                        <p class="mt-2 text-xs text-white/55">{{ item.body }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-12 border-b border-[#1a1712] px-6 py-16 lg:grid-cols-[0.72fr_1.28fr]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#c79b55]">{{ approach.eyebrow }}</p>
                <h2 class="mt-5 max-w-sm text-4xl font-light uppercase leading-tight">{{ approach.heading }}</h2>
                <p class="mt-6 max-w-md text-sm leading-7 text-white/62">{{ approach.body }}</p>
                <Link href="/methodology" class="mt-9 inline-flex border border-white/20 px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em]">Learn our process <span class="ml-4">--></span></Link>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="([title, body], index) in Object.entries(approach.content?.steps || {})" :key="title" class="min-h-56 bg-[#121212] p-7 shadow-[inset_0_0_0_1px_rgba(255,255,255,0.04)]">
                    <component :is="approachIcons[index] || Compass" class="size-8 text-[#c79b55]" />
                    <h3 class="mt-12 text-sm font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ title }}</h3>
                    <p class="mt-4 text-sm leading-6 text-white/58">{{ body }}</p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl border-b border-[#1a1712] px-6 py-14">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#c79b55]">{{ market.eyebrow }}</p>
                    <h2 class="mt-3 text-2xl font-semibold">{{ market.heading }}</h2>
                </div>
                <Link href="/insights" class="hidden text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55] md:block">View all insights --></Link>
            </div>
            <div class="mt-7 grid gap-3 md:grid-cols-5">
                <div v-for="(signal, index) in market.content?.signals || []" :key="signal.title" class="relative min-h-44 overflow-hidden bg-[#111] p-5 shadow-[inset_0_0_0_1px_rgba(255,255,255,0.05)]">
                    <div class="flex justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ signal.label }}</p>
                            <p class="mt-1 text-xs text-white/58">{{ signal.title }}</p>
                        </div>
                        <component :is="signalIcons[index] || LineChart" class="size-5 text-white/25" />
                    </div>
                    <p class="mt-8 text-3xl font-semibold">{{ signal.value }}</p>
                    <p class="mt-1 text-xs text-white/58">{{ signal.meta }}</p>
                    <p class="mt-5 text-xs text-[#c79b55]">{{ signal.change }}</p>

                    <div class="absolute bottom-4 right-4 h-14 w-32 opacity-80">
                        <svg v-if="index !== 3" viewBox="0 0 126 56" class="size-full" aria-hidden="true">
                            <defs>
                                <linearGradient :id="`signalGradient${index}`" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#c79b55" stop-opacity="0.32" />
                                    <stop offset="100%" stop-color="#c79b55" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path :d="`M0,56 L${sparklinePoints(signalSeries[index] || signalSeries[0])} L126,56 Z`" :fill="`url(#signalGradient${index})`" opacity="0.65" />
                            <polyline :points="sparklinePoints(signalSeries[index] || signalSeries[0])" fill="none" stroke="#c79b55" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <g fill="#c79b55">
                                <circle v-for="(point, pointIndex) in (signalSeries[index] || signalSeries[0])" :key="pointIndex" :cx="(pointIndex / Math.max((signalSeries[index] || signalSeries[0]).length - 1, 1)) * 126" :cy="48 - ((point - Math.min(...(signalSeries[index] || signalSeries[0]))) / Math.max(Math.max(...(signalSeries[index] || signalSeries[0])) - Math.min(...(signalSeries[index] || signalSeries[0])), 1)) * 48" r="1.5" opacity="0.55" />
                            </g>
                        </svg>
                        <svg v-else viewBox="0 0 126 56" class="size-full" aria-hidden="true">
                            <rect v-for="(value, barIndex) in signalSeries[index]" :key="barIndex" :x="barIndex * 19 + 4" :y="56 - value * 0.72" width="10" :height="value * 0.72" rx="1.5" fill="#c79b55" :opacity="barIndex === signalSeries[index].length - 1 ? 0.95 : 0.35 + barIndex * 0.08" />
                            <line x1="0" y1="55" x2="126" y2="55" stroke="white" stroke-opacity="0.12" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl border-b border-[#1a1712] px-6 py-14">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#c79b55]">{{ thesesSection.eyebrow }}</p>
                    <h2 class="mt-3 text-2xl font-semibold">{{ thesesSection.heading }}</h2>
                </div>
                <Link href="/theses" class="hidden text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55] md:block">View all theses --></Link>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <Link v-for="thesis in featuredTheses" :key="thesis.id" :href="`/theses/${thesis.slug}`" class="group bg-[#101010] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.06)]">
                    <div class="aspect-[16/7] overflow-hidden bg-[#151515]">
                        <img v-if="imageUrl(thesis.featured_image_path)" :src="imageUrl(thesis.featured_image_path) || ''" :alt="thesis.title" class="size-full object-cover opacity-85 transition duration-500 group-hover:scale-105 group-hover:opacity-100" />
                        <div v-else class="size-full bg-[radial-gradient(circle_at_70%_20%,rgba(199,155,85,0.25),transparent_30%),linear-gradient(135deg,#202020,#090909)]" />
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ thesis.city }} thesis</p>
                        <h3 class="mt-3 text-lg font-semibold leading-snug">{{ thesis.title }}</h3>
                        <p class="mt-4 line-clamp-2 text-sm leading-6 text-white/58">{{ thesis.executive_summary }}</p>
                        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.14em] text-[#c79b55]">Read thesis --></p>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mx-auto max-w-7xl border-b border-[#1a1712] px-6 py-14">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#c79b55]">{{ positionsSection.eyebrow }}</p>
                    <h2 class="mt-3 text-2xl font-semibold">{{ positionsSection.heading }}</h2>
                </div>
                <Link href="/positions" class="hidden text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55] md:block">View all properties --></Link>
            </div>
            <div class="mt-8 flex items-stretch gap-4 overflow-x-auto pb-2">
                <button class="my-auto grid size-10 shrink-0 place-items-center rounded-full border border-white/10 text-white/45"><ChevronLeft class="size-4" /></button>
                <Link v-for="position in featuredPositions" :key="position.id" :href="`/positions/${position.slug}`" class="min-w-[230px] bg-[#101010] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.06)]">
                    <div class="aspect-[16/9] bg-[radial-gradient(circle_at_70%_18%,rgba(199,155,85,0.20),transparent_30%),linear-gradient(135deg,#252525,#090909)]" />
                    <div class="p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#c79b55]">{{ position.city }}</p>
                        <h3 class="mt-3 min-h-12 text-base font-semibold">{{ position.project_name }}</h3>
                        <p class="mt-4 text-[10px] uppercase tracking-[0.14em] text-white/40">Starting from</p>
                        <p class="mt-1 text-sm text-white">{{ position.expected_yield_range }} yield</p>
                        <p class="mt-5 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#c79b55]">View details --></p>
                    </div>
                </Link>
                <button class="my-auto grid size-10 shrink-0 place-items-center rounded-full border border-white/10 text-white/45"><ChevronRight class="size-4" /></button>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-10 border-b border-[#1a1712] px-6 py-16 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="grid min-h-[330px] content-end bg-[radial-gradient(circle_at_50%_18%,rgba(255,255,255,0.18),transparent_18%),linear-gradient(145deg,#2a2a2a,#080808)] p-6 grayscale">
                <p class="text-xs uppercase tracking-[0.18em] text-white/45">Founder portrait</p>
            </div>
            <div class="grid gap-8 lg:grid-cols-[1fr_0.85fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#c79b55]">{{ founder.eyebrow }}</p>
                    <h2 class="mt-3 text-4xl font-light uppercase">{{ founder.heading }}</h2>
                    <blockquote class="mt-8 border-l-2 border-[#c79b55] pl-5 text-2xl leading-snug">{{ founder.body }}</blockquote>
                    <p class="mt-7 text-sm leading-7 text-white/60">{{ founder.content?.bio }}</p>
                    <Link href="/about" class="mt-7 inline-flex border border-white/20 px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em]">Read more --></Link>
                </div>
                <div class="grid content-center gap-7">
                    <div v-for="stat in founder.content?.stats || []" :key="stat.label" class="flex items-center gap-5">
                        <LockKeyhole class="size-8 text-[#c79b55]" />
                        <div>
                            <p class="text-3xl font-semibold">{{ stat.value }}</p>
                            <p class="text-xs uppercase tracking-[0.16em] text-white/45">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(199,155,85,0.16),transparent_24%),linear-gradient(180deg,#101010,#050505)]" />
            <div class="relative mx-auto max-w-7xl px-6 py-12 text-center">
                <h2 class="text-4xl font-light uppercase tracking-[0.08em]">{{ cta.heading }}</h2>
                <p class="mt-4 text-sm text-white/58">{{ cta.body }}</p>
                <Link href="/contact" class="mt-6 inline-flex border border-[#c79b55] px-5 py-3 text-xs font-semibold uppercase tracking-[0.16em] text-[#c79b55]">Get in touch --></Link>
            </div>
        </section>

        <footer class="border-t border-[#1a1712] bg-[#070707] px-6 py-10">
            <div class="mx-auto grid max-w-7xl gap-8 text-xs text-white/48 md:grid-cols-[1.3fr_1fr_1fr_1fr]">
                <div>
                    <p class="text-lg font-semibold uppercase tracking-[0.14em] text-white">Global Value Thesis</p>
                    <p class="mt-4 max-w-xs leading-6">A structured view on value for investors who think in cycles, not launches.</p>
                </div>
                <div><p class="mb-3 text-[#c79b55]">Company</p><p>About Us</p><p class="mt-2">Our Approach</p><p class="mt-2">Careers</p></div>
                <div><p class="mb-3 text-[#c79b55]">Solutions</p><p>Investment Theses</p><p class="mt-2">Property Advisory</p><p class="mt-2">Market Intelligence</p></div>
                <div><p class="mb-3 text-[#c79b55]">Contact</p><p>research@gvt.test</p><p class="mt-2">+20 100 000 0000</p></div>
            </div>
        </footer>
    </div>
</template>
