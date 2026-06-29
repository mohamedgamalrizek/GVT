<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import { Form } from '@inertiajs/vue3';

defineProps<{ position: any }>();
</script>

<template>
    <PublicLayout :title="position.project_name">
        <section class="mx-auto max-w-6xl px-5 py-20 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#f2cf74]">{{ position.asset_type }} / {{ position.city }}</p>
            <h1 class="mt-5 text-4xl font-semibold md:text-6xl">{{ position.project_name }}</h1>
            <p class="mt-6 max-w-3xl text-xl leading-8 text-white/68">{{ position.thesis_summary }}</p>
            <div class="mt-10 grid gap-4 md:grid-cols-4">
                <div v-for="[label, value] in [['Developer', position.developer?.name], ['Yield', position.expected_yield_range], ['Appreciation', position.expected_appreciation_range], ['Risk', position.risk_level]]" :key="label" class="border border-white/10 p-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/45">{{ label }}</p>
                    <p class="mt-3 text-xl text-white">{{ value }}</p>
                </div>
            </div>
            <div class="mt-12 grid gap-8 md:grid-cols-[1fr_360px]">
                <div class="grid gap-8">
                    <section v-for="[label, body] in [['Investment rationale', position.investment_rationale], ['Developer certification', position.developer?.evaluation_summary], ['Location intelligence', position.location_intelligence]]" :key="label" class="border-t border-white/10 pt-7">
                        <h2 class="text-2xl font-semibold">{{ label }}</h2>
                        <p class="mt-4 leading-7 text-white/62">{{ body }}</p>
                    </section>
                </div>
                <Form action="/contact" method="post" class="h-fit border border-[#d7b55d]/40 p-6" #default="{ processing, errors }">
                    <h2 class="text-2xl font-semibold">Position inquiry</h2>
                    <input type="hidden" name="inquiry_type" value="Investor inquiry" />
                    <input type="hidden" name="subject" :value="`Inquiry: ${position.project_name}`" />
                    <div class="mt-5 grid gap-3">
                        <input name="name" placeholder="Name" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                        <input name="email" type="email" placeholder="Email" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                        <input name="company" placeholder="Company" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                        <textarea name="message" rows="4" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm">I would like to review the investment rationale and document set.</textarea>
                        <p v-if="Object.keys(errors).length" class="text-sm text-[#f2cf74]">Please complete the required fields.</p>
                        <button :disabled="processing" class="bg-[#f2cf74] px-4 py-3 text-sm font-semibold text-black">Submit Inquiry</button>
                    </div>
                </Form>
            </div>
        </section>
    </PublicLayout>
</template>
