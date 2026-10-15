<script setup>
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t, locRoute } = useI18n();
const page = usePage();
const showingNavigationDropdown = ref(false);

const seo = computed(() => page.props.seo || {});
const ogImage = 'https://demo-concierge.dev4rweb.com/og-image.jpg';
</script>

<template>
    <div class="min-h-screen">
        <Head>
            <meta v-if="seo.description" head-key="description" name="description" :content="seo.description" />
            <meta v-if="seo.title" head-key="og:title" property="og:title" :content="seo.title" />
            <meta v-if="seo.description" head-key="og:description" property="og:description" :content="seo.description" />
            <meta head-key="og:image" property="og:image" :content="ogImage" />
            <meta v-if="seo.title" head-key="twitter:title" name="twitter:title" :content="seo.title" />
            <meta v-if="seo.description" head-key="twitter:description" name="twitter:description" :content="seo.description" />
            <meta head-key="twitter:image" name="twitter:image" :content="ogImage" />
        </Head>

        <header class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-5 sm:px-6">
            <Link :href="locRoute('welcome')" class="flex min-w-0 items-center gap-2">
                <ApplicationLogo class="h-9 w-9 shrink-0" />
                <span class="truncate text-sm font-semibold tracking-tight">{{ t('app_name') }}</span>
            </Link>

            <nav class="hidden items-center gap-1 sm:flex" :aria-label="t('nav.menu')">
                <Link
                    :href="locRoute('about')"
                    class="rounded-full px-3 py-2 text-sm transition"
                    :class="route().current('about') || route().current('locale.about') ? 'text-ink' : 'text-muted hover:text-ink'"
                >
                    {{ t('nav.about') }}
                </Link>
                <Link
                    :href="locRoute('guide')"
                    class="rounded-full px-3 py-2 text-sm transition"
                    :class="route().current('guide') || route().current('locale.guide') ? 'text-ink' : 'text-muted hover:text-ink'"
                >
                    {{ t('nav.guide') }}
                </Link>
            </nav>

            <div class="flex items-center gap-2">
                <ThemeToggle />
                <LocaleSwitcher />
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink sm:hidden"
                    :aria-label="showingNavigationDropdown ? t('nav.close_menu') : t('nav.menu')"
                    :aria-expanded="showingNavigationDropdown"
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                >
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            :class="{ hidden: showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{ hidden: !showingNavigationDropdown }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </header>

        <div v-if="showingNavigationDropdown" class="border-b border-line bg-surface sm:hidden">
            <nav class="mx-auto flex max-w-6xl flex-col px-4 py-2" :aria-label="t('nav.menu')">
                <Link
                    :href="locRoute('about')"
                    class="rounded-xl px-3 py-2.5 text-sm font-medium text-ink"
                    @click="showingNavigationDropdown = false"
                >
                    {{ t('nav.about') }}
                </Link>
                <Link
                    :href="locRoute('guide')"
                    class="rounded-xl px-3 py-2.5 text-sm font-medium text-ink"
                    @click="showingNavigationDropdown = false"
                >
                    {{ t('nav.guide') }}
                </Link>
            </nav>
        </div>

        <slot />

        <footer class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <p class="text-center text-sm text-muted">
                {{ t('about.built_by') }}
                <a href="https://dev4rweb.com" class="text-accent underline-offset-2 hover:underline" target="_blank" rel="noopener noreferrer">
                    dev4rweb
                </a>
            </p>
        </footer>
    </div>
</template>
