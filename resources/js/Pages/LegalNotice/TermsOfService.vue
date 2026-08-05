<script setup>
import {Head} from "@inertiajs/vue3";

const lastUpdated = 'August 5, 2026'
const supportEmail = 'support@myunially.com.ng'
const operatorName = 'Atuo Israel Beshuwhobel'
</script>

<template>
    <Head title="Terms of service" />
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 py-10">

            <div class="mb-8">
                <p class="text-xs font-semibold tracking-wide text-primary uppercase mb-1">Legal</p>
                <h1 class="text-2xl font-bold text-slate-900">Terms of Service</h1>
                <p class="text-sm text-slate-400 mt-1">Last updated: {{ lastUpdated }}</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-8 text-sm leading-relaxed text-slate-700">

                <section>
                    <p>
                        These Terms of Service ("Terms") govern your use of myUniAlly, operated by
                        {{ operatorName }} ("we," "us"). By creating an account or using myUniAlly,
                        you agree to these Terms. If you don't agree, please don't use the Service.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">1. Who Can Use myUniAlly</h2>
                    <p>
                        myUniAlly is built for students, contributors, and staff at Nigerian tertiary
                        institutions. You must provide accurate information when creating an account,
                        including your correct programme, level, and school where applicable.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">2. Your Account</h2>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>You're responsible for keeping your login credentials secure.</li>
                        <li>You're responsible for all activity that happens under your account.</li>
                        <li>Notify us immediately if you suspect unauthorized access to your account.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">3. Contributed Content</h2>
                    <p class="mb-2">
                        If you contribute past questions, timetable data, or other academic content:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>You confirm you have the right to share it, and that it doesn't infringe on anyone else's rights.</li>
                        <li>You grant myUniAlly a non-exclusive, royalty-free license to display, distribute, and use that content within the Service.</li>
                        <li>You're responsible for the accuracy of what you submit — we do our best to review contributions, but we can't guarantee every submission is error-free.</li>
                        <li>We may remove content that's inaccurate, infringing, or violates these Terms, at our discretion.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">4. Acceptable Use</h2>
                    <p class="mb-2">You agree not to:</p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>Use automated tools, scripts, or bots to interact with the Service (including Question of the Day or exam practice features) in ways not intended for normal human use.</li>
                        <li>Attempt to bypass restrictions we've put in place, such as answer-input protections.</li>
                        <li>Upload false, misleading, or plagiarized academic content.</li>
                        <li>Use the Service to harass, impersonate, or harm other users.</li>
                        <li>Attempt to gain unauthorized access to other accounts or to our systems.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">5. Educational Content Disclaimer</h2>
                    <p>
                        Past questions, exam practice content, and Question of the Day items are provided
                        for study and revision purposes. While we and our contributors aim for accuracy,
                        we don't guarantee that all content is complete, error-free, or reflective of your
                        institution's current curriculum or exam format. Use it as a study aid, not a
                        substitute for your official course materials.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">6. Push Notifications</h2>
                    <p>
                        If you enable push notifications, we'll send you alerts such as lecture reminders
                        and daily question prompts. You can disable these at any time through your device
                        or browser notification settings.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">7. Intellectual Property</h2>
                    <p>
                        The myUniAlly name, logo, design, and underlying software are our property or
                        licensed to us. Contributed academic content remains subject to the license
                        described in Section 3. You may not copy, resell, or redistribute the Service
                        or its non-user-generated content without our permission.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">8. Termination</h2>
                    <p>
                        We may suspend or terminate your account if you violate these Terms, misuse the
                        Service, or submit harmful or fraudulent content. You may stop using the Service
                        and request account deletion at any time by contacting us.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">9. Disclaimer & Limitation of Liability</h2>
                    <p>
                        myUniAlly is provided "as is," without warranties of any kind. We don't guarantee
                        uninterrupted or error-free operation. To the fullest extent permitted by law, we
                        aren't liable for any indirect, incidental, or consequential damages arising from
                        your use of the Service, including academic outcomes based on content found here.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">10. Changes to These Terms</h2>
                    <p>
                        We may update these Terms from time to time. Continued use of myUniAlly after
                        changes take effect means you accept the updated Terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">11. Governing Law</h2>
                    <p>
                        These Terms are governed by the laws of the Federal Republic of Nigeria.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">12. Contact Us</h2>
                    <p>
                        Questions about these Terms? Reach out at
                        <a :href="`mailto:${supportEmail}`" class="text-primary font-medium">{{ supportEmail }}</a>.
                    </p>
                </section>

            </div>
        </div>
    </div>
</template>
