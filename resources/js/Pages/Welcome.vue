<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

defineProps({
    botUsername: String,
});

const { t, locRoute } = useI18n();
const page = usePage();
const demo = computed(() => page.props.demo || {});
const stack = ['Laravel', 'Vue 3', 'Inertia.js', 'Tailwind CSS', 'Telegram Bot API', 'OpenRouter AI', 'MySQL'];
</script>

<template>
    <PublicLayout>
        <Head :title="t('welcome.title')" />

        <main class="mx-auto max-w-6xl px-4 pb-8 sm:px-6">
            <section class="mx-auto max-w-3xl py-8 text-center sm:py-14">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-accent">Portfolio demo</p>
                <h1 class="text-balance text-4xl font-semibold tracking-tight text-ink sm:text-5xl">
                    {{ t('welcome.title') }}
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-pretty text-base text-muted sm:text-lg">
                    {{ t('welcome.tagline') }}
                </p>
            </section>

            <section class="surface-card mx-auto max-w-3xl p-5 sm:p-8">
                <h2 class="text-xl font-semibold text-ink">{{ t('welcome.about_title') }}</h2>
                <p class="mt-3 text-sm leading-7 text-muted sm:text-base">
                    {{ t('welcome.about_body') }}
                </p>
                <div class="mt-6 border-t border-line pt-6">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted">{{ t('welcome.stack_title') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="item in stack"
                            :key="item"
                            class="rounded-full border border-line bg-surface-2 px-3 py-1 text-xs font-medium text-ink"
                        >
                            {{ item }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="mx-auto mt-6 grid max-w-3xl gap-4 sm:grid-cols-2">
                <a
                    v-if="botUsername"
                    :href="`https://t.me/${botUsername}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="soft-ring surface-card block p-6 text-center"
                >
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.open_bot') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.open_bot_hint') }}</p>
                </a>

                <Link :href="locRoute('admin.dashboard')" class="soft-ring surface-card block p-6 text-center">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.admin_panel') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.admin_panel_hint') }}</p>
                </Link>

                <Link :href="locRoute('about')" class="soft-ring surface-card block p-6 text-center">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.product') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.product_hint') }}</p>
                </Link>

                <Link :href="locRoute('guide')" class="soft-ring surface-card block p-6 text-center">
                    <h3 class="text-xl font-semibold text-ink">{{ t('welcome.how_to') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ t('welcome.how_to_hint') }}</p>
                </Link>
            </section>

            <aside class="mx-auto mt-6 max-w-3xl rounded-2xl border border-line bg-surface/70 px-5 py-4 text-center text-sm text-muted">
                <p class="mb-1">
                    <strong class="text-ink">{{ t('welcome.demo_credentials') }}:</strong>
                    {{ demo.email }} / {{ demo.password }}
                </p>
                <p>
                    GitHub:
                    <a href="https://github.com/dev4rweb/demo-concierge" class="text-accent underline-offset-2 hover:underline" target="_blank" rel="noopener noreferrer">
                        dev4rweb/demo-concierge
                    </a>
                </p>
            </aside>
        </main>
    </PublicLayout>
</template>
