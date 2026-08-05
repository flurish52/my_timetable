<script setup>
import {Head} from "@inertiajs/vue3";

const lastUpdated = 'August 5, 2026'
const supportEmail = 'support@myunially.com.ng'
const operatorName = 'Atuo Israel Beshuwhobel'
</script>

<template>
    <Head title="Privacy policy" />
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-3xl mx-auto px-4 py-10">

            <div class="mb-8">
                <p class="text-xs font-semibold tracking-wide text-primary uppercase mb-1">Legal</p>
                <h1 class="text-2xl font-bold text-slate-900">Privacy Policy</h1>
                <p class="text-sm text-slate-400 mt-1">Last updated: {{ lastUpdated }}</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 space-y-8 text-sm leading-relaxed text-slate-700">

                <section>
                    <p>
                        myUniAlly ("we," "us," "the Service") is operated by {{ operatorName }},
                        an individual developer based in Nigeria. This Privacy Policy explains what
                        information we collect when you use myUniAlly, how we use it, and the choices
                        you have. By using myUniAlly, you agree to the practices described here.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">1. Information We Collect</h2>
                    <p class="mb-2">We collect the following categories of information:</p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li><span class="font-semibold">Account information:</span> your name, email address, school, programme, and level, provided when you register.</li>
                        <li><span class="font-semibold">Usage data:</span> which past questions, timetable entries, and Question of the Day items you view or attempt, including your answers and streaks.</li>
                        <li><span class="font-semibold">Device and notification data:</span> a device token used to deliver push notifications (e.g. timetable reminders, Question of the Day alerts) via Firebase Cloud Messaging, a Google service.</li>
                        <li><span class="font-semibold">Contributed content:</span> if you upload or submit past questions, timetable info, or other academic content, we store that content and associate it with your contributor profile.</li>
                        <li><span class="font-semibold">Technical data:</span> standard log data such as IP address, browser type, and access times, collected automatically for security and diagnostics.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">2. How We Use Your Information</h2>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>To provide core features: timetables, past questions, exam practice, and Question of the Day.</li>
                        <li>To send push notifications you've opted into (lecture reminders, daily questions, streak alerts).</li>
                        <li>To attribute contributed content to the right contributor and maintain the Contributors Board.</li>
                        <li>To maintain and improve the Service, including diagnosing bugs and understanding usage patterns.</li>
                        <li>To communicate with you about your account or important changes to the Service.</li>
                    </ul>
                    <p class="mt-2">
                        We do not sell your personal information to third parties.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">3. Third-Party Services</h2>
                    <p>
                        We use a limited number of third-party services to operate myUniAlly, including:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 mt-2">
                        <li><span class="font-semibold">Firebase Cloud Messaging (Google):</span> to deliver push notifications to your device.</li>
                        <li><span class="font-semibold">Google Analytics:</span> to understand how visitors use myUniAlly, such as which pages are viewed and how the site is accessed. This may involve cookies and similar technologies, and collects information like your IP address, device type, and browsing behavior on our site.</li>
                        <li><span class="font-semibold">Google Search Console:</span> used to monitor and improve our presence in search results. This tool doesn't access or process personal data about individual visitors.</li>
                        <li><span class="font-semibold">Our hosting provider:</span> to store data and run the application.</li>
                    </ul>
                    <p class="mt-2">
                        These providers only receive the information necessary to perform their function
                        and are not permitted to use your data for their own purposes. You can control
                        cookie-based tracking through your browser settings.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">4. Data Retention</h2>
                    <p>
                        We retain your account and usage data for as long as your account remains active.
                        If you delete your account, we will delete or anonymize your personal information
                        within a reasonable period, except where we're required to retain it for legal or
                        security reasons.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">5. Your Rights</h2>
                    <p class="mb-2">
                        Under the Nigeria Data Protection Act (NDPA) 2023, you have the right to:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5">
                        <li>Access the personal data we hold about you.</li>
                        <li>Request correction of inaccurate data.</li>
                        <li>Request deletion of your data, subject to legal or operational limits.</li>
                        <li>Withdraw consent for optional features, such as push notifications, at any time.</li>
                        <li>Object to certain uses of your data.</li>
                    </ul>
                    <p class="mt-2">
                        To exercise any of these rights, contact us at
                        <a :href="`mailto:${supportEmail}`" class="text-primary font-medium">{{ supportEmail }}</a>.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">6. Data Security</h2>
                    <p>
                        We take reasonable technical and organizational measures to protect your information
                        against unauthorized access, loss, or misuse. However, no method of transmission or
                        storage is 100% secure, and we cannot guarantee absolute security.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">7. Children's Privacy</h2>
                    <p>
                        myUniAlly is intended for university and tertiary institution students and is not
                        directed at children under 13. We do not knowingly collect personal information
                        from children under 13. If you believe a child has provided us with personal
                        information, please contact us so we can remove it.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">8. Changes to This Policy</h2>
                    <p>
                        We may update this Privacy Policy from time to time. If we make material changes,
                        we'll update the "Last updated" date above and, where appropriate, notify you
                        directly.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-bold text-slate-900 mb-2">9. Contact Us</h2>
                    <p>
                        If you have questions about this Privacy Policy or how your data is handled,
                        reach out at
                        <a :href="`mailto:${supportEmail}`" class="text-primary font-medium">{{ supportEmail }}</a>.
                    </p>
                </section>

            </div>
        </div>
    </div>
</template>
