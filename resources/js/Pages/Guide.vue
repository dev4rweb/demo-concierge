<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps({
    botUsername: String,
});

const { t, locRoute } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('guide.title')" />

        <main class="mx-auto max-w-4xl px-4 pb-8 sm:px-6">
            <h1 class="text-3xl font-semibold tracking-tight text-ink sm:text-4xl">{{ t('guide.title') }}</h1>
            <p class="mt-3 max-w-2xl text-muted">{{ t('guide.subtitle') }}</p>

            <div class="mt-8 grid gap-5 lg:grid-cols-2">
                <section class="surface-card p-5 sm:p-7">
                    <h2 class="text-xl font-semibold text-ink">{{ t('guide.client_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ t('guide.client_intro') }}</p>
                    <ol class="mt-4 space-y-3 text-sm leading-6 text-ink">
                        <li>{{ t('guide.client_find_bot') }}</li>
                        <li>{{ t('guide.client_ask') }}</li>
                        <li>{{ t('guide.client_contacts') }}</li>
                        <li>{{ t('guide.client_manager') }}</li>
                    </ol>
                    <a
                        v-if="botUsername"
                        :href="`https://t.me/${botUsername}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-6 inline-flex rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-accent-fg"
                    >
                        {{ t('guide.open_bot') }}
                    </a>
                </section>

                <section class="surface-card p-5 sm:p-7">
                    <h2 class="text-xl font-semibold text-ink">{{ t('guide.admin_title') }}</h2>
                    <ol class="mt-4 space-y-3 text-sm leading-6 text-ink">
                        <li>{{ t('guide.admin_dashboard') }}</li>
                        <li>{{ t('guide.admin_dialogs') }}</li>
                        <li>{{ t('guide.admin_attention') }}</li>
                        <li>{{ t('guide.admin_reply') }}</li>
                        <li>{{ t('guide.admin_leads') }}</li>
                        <li>{{ t('guide.admin_kb') }}</li>
                    </ol>
                    <Link
                        :href="locRoute('admin.dashboard')"
                        class="mt-6 inline-flex rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent"
                    >
                        {{ t('guide.open_admin') }}
                    </Link>
                </section>
            </div>
        </main>
    </PublicLayout>
</template>
