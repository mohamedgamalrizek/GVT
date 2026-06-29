<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';
import { Form } from '@inertiajs/vue3';
</script>

<template>
    <PublicLayout title="Newsletter">
        <section class="mx-auto grid max-w-6xl gap-10 px-5 py-20 md:grid-cols-[1fr_420px] lg:px-8">
            <SectionHeader eyebrow="Research brief" title="A measured cadence for market intelligence." body="Subscribe for thesis updates, developer certification notes, and cycle-aware allocation commentary." />
            <Form action="/newsletter" method="post" class="border border-[#d7b55d]/40 p-6" #default="{ processing, errors, recentlySuccessful }">
                <div class="grid gap-3">
                    <input name="name" placeholder="Name" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <input name="email" type="email" placeholder="Email" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <select name="investor_type" class="border border-white/10 bg-[#080808] px-4 py-3 text-sm">
                        <option>Egyptian HNWI</option>
                        <option>Diaspora Investor</option>
                        <option>Developer</option>
                    </select>
                    <p v-if="Object.keys(errors).length" class="text-sm text-[#f2cf74]">Check the email field.</p>
                    <p v-if="recentlySuccessful" class="text-sm text-[#f2cf74]">Subscription recorded.</p>
                    <button :disabled="processing" class="bg-[#f2cf74] px-4 py-3 text-sm font-semibold text-black">Subscribe</button>
                </div>
            </Form>
        </section>
    </PublicLayout>
</template>
