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

const translationList = (key) => {
    const value = t(key);
    return Array.isArray(value) ? value : [];
};

const whatPoints = computed(() => translationList('about.what_points'));
const howSteps = computed(() => translationList('about.how'));
const uses = computed(() => translationList('about.uses'));
const customerPoints = computed(() => translationList('about.customer'));
const adminPoints = computed(() => translationList('about.admin'));
const demo = computed(() => page.props.demo || {});
const stack = ['Laravel', 'Vue 3', 'Telegram', 'MySQL'];
</script>

<template>
    <PublicLayout>
        <Head :title="t('about.title')" />

        <main class="mx-auto max-w-6xl px-4 pb-8 sm:px-6">
            <section class="mx-auto max-w-3xl py-6 text-center sm:py-12">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-accent">
                    {{ t('about.kicker') }}
                </p>
                <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink sm:text-5xl">
                    {{ t('about.title') }}
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-pretty text-base leading-7 text-muted sm:text-lg">
                    {{ t('about.lede') }}
                </p>
                <div class="mt-7 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
                    <a
                        v-if="botUsername"
                        :href="`https://t.me/${botUsername}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-xl bg-accent px-5 py-2.5 text-sm font-semibold text-accent-fg"
                    >
                        {{ t('about.open_bot') }}
                    </a>
                    <Link
                        :href="locRoute('admin.dashboard')"
                        class="inline-flex items-center justify-center rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-ink transition hover:border-accent"
                    >
                        {{ t('about.open_admin') }}
                    </Link>
                </div>
            </section>

            <section class="surface-card mx-auto max-w-3xl p-5 sm:p-8">
                <h2 class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.what_title') }}</h2>
                <p class="mt-3 text-sm leading-7 text-muted sm:text-base">{{ t('about.what_body') }}</p>
                <ul class="mt-5 space-y-3 text-start text-sm leading-6 text-ink">
                    <li v-for="(point, index) in whatPoints" :key="index" class="flex gap-3">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                        <span>{{ point }}</span>
                    </li>
                </ul>
            </section>

            <section class="mx-auto mt-10 max-w-5xl">
                <h2 class="text-center text-xl font-semibold text-ink sm:text-2xl">{{ t('about.how_title') }}</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="(step, index) in howSteps"
                        :key="index"
                        class="surface-card p-5 sm:p-6"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent">
                            {{ String(index + 1).padStart(2, '0') }}
                        </p>
                        <h3 class="mt-3 text-lg font-semibold text-ink">{{ step.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-muted">{{ step.body }}</p>
                    </article>
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-5xl">
                <h2 class="text-center text-xl font-semibold text-ink sm:text-2xl">{{ t('about.uses_title') }}</h2>
                <p class="mx-auto mt-3 max-w-2xl text-center text-sm leading-6 text-muted">
                    {{ t('about.uses_intro') }}
                </p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <article v-for="(item, index) in uses" :key="index" class="surface-card p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-ink">{{ item.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-muted">{{ item.scenario }}</p>
                        <p class="mt-4 rounded-xl bg-surface-2 px-3 py-2 text-sm font-medium text-ink">
                            {{ item.example }}
                        </p>
                    </article>
                </div>
            </section>

            <section class="mx-auto mt-12 max-w-5xl">
                <h2 class="text-center text-xl font-semibold text-ink sm:text-2xl">{{ t('about.roles_title') }}</h2>
                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    <article class="surface-card p-5 sm:p-7">
                        <h3 class="text-lg font-semibold text-ink">{{ t('about.customer_title') }}</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-ink">
                            <li v-for="(point, index) in customerPoints" :key="index" class="flex gap-3">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </article>
                    <article class="surface-card p-5 sm:p-7">
                        <h3 class="text-lg font-semibold text-ink">{{ t('about.admin_title') }}</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-ink">
                            <li v-for="(point, index) in adminPoints" :key="index" class="flex gap-3">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </article>
                </div>
            </section>

            <section class="soft-ring surface-card mx-auto mt-12 max-w-3xl p-5 text-center sm:p-8">
                <h2 class="text-xl font-semibold text-ink sm:text-2xl">{{ t('about.portfolio_title') }}</h2>
                <p class="mt-3 text-sm leading-7 text-muted sm:text-base">{{ t('about.portfolio_body') }}</p>
                <a
                    href="https://dev4rweb.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-5 inline-flex rounded-xl bg-accent px-5 py-2.5 text-sm font-semibold text-accent-fg"
                >
                    {{ t('about.portfolio_cta') }}
                </a>
            </section>

            <section class="mx-auto mt-8 max-w-3xl rounded-2xl border border-line bg-surface/70 p-5 text-center sm:p-7">
                <h2 class="text-xl font-semibold text-ink">{{ t('about.demo_title') }}</h2>
                <p class="mt-3 text-sm leading-6 text-muted">{{ t('about.demo_body') }}</p>
                <p class="mt-4 text-sm text-muted">
                    <strong class="text-ink">{{ t('about.credentials') }}:</strong>
                    {{ demo.email }} / {{ demo.password }}
                </p>
                <div class="mt-5 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
                    <Link
                        :href="locRoute('guide')"
                        class="inline-flex items-center justify-center rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent"
                    >
                        {{ t('about.open_guide') }}
                    </Link>
                    <Link
                        :href="locRoute('admin.dashboard')"
                        class="inline-flex items-center justify-center rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent"
                    >
                        {{ t('about.open_admin') }}
                    </Link>
                </div>
            </section>

            <section class="mx-auto mt-10 max-w-3xl text-center">
                <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ t('about.stack_title') }}</h2>
                <p class="mt-2 text-xs leading-5 text-muted">{{ t('about.stack_note') }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    <span
                        v-for="item in stack"
                        :key="item"
                        class="rounded-full border border-line bg-surface px-3 py-1 text-[11px] font-medium text-muted"
                    >
                        {{ item }}
                    </span>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>
