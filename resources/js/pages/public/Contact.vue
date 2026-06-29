<script setup lang="ts">
import PublicLayout from '@/components/public/PublicLayout.vue';
import SectionHeader from '@/components/public/SectionHeader.vue';
import { Form } from '@inertiajs/vue3';
</script>

<template>
    <PublicLayout title="Contact">
        <section class="mx-auto grid max-w-6xl gap-10 px-5 py-20 md:grid-cols-[1fr_480px] lg:px-8">
            <SectionHeader eyebrow="Contact" title="Start with the investment question." body="Send a focused inquiry. The response will be matched to investor advisory, developer certification, media, or general context." />
            <Form action="/contact" method="post" class="border border-white/10 p-6" #default="{ processing, errors, recentlySuccessful }">
                <div class="grid gap-3">
                    <input name="name" placeholder="Name" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <input name="email" type="email" placeholder="Email" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <input name="company" placeholder="Company" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <select name="inquiry_type" class="border border-white/10 bg-[#080808] px-4 py-3 text-sm">
                        <option>Investor inquiry</option>
                        <option>Developer certification inquiry</option>
                        <option>Media inquiry</option>
                        <option>General</option>
                    </select>
                    <input name="subject" placeholder="Subject" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <textarea name="message" rows="6" placeholder="Message" class="border border-white/10 bg-white/[0.04] px-4 py-3 text-sm" />
                    <p v-if="Object.keys(errors).length" class="text-sm text-[#f2cf74]">Please complete the required fields.</p>
                    <p v-if="recentlySuccessful" class="text-sm text-[#f2cf74]">Inquiry received.</p>
                    <button :disabled="processing" class="bg-[#f2cf74] px-4 py-3 text-sm font-semibold text-black">Send Inquiry</button>
                </div>
            </Form>
        </section>
    </PublicLayout>
</template>
