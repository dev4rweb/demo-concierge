<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-ink">
                {{ t('dashboard.title') }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="surface-card p-6 text-ink">
                    {{ t('nav.dashboard') }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
