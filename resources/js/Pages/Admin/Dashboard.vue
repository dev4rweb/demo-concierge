<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    conversations: Object,
    stats: Object,
});

const { t, locRoute, localeTag } = useI18n();

const formatDate = (date) => {
    return new Date(date).toLocaleString(localeTag.value, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const roleLabel = (role) => {
    if (role === 'user') return t('dashboard.client');
    if (role === 'admin') return t('dashboard.admin');
    return t('dashboard.bot');
};
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-ink">{{ t('dashboard.title') }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-5">
                    <div class="surface-card p-4 sm:p-6">
                        <div class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.total_conversations') }}</div>
                        <div class="mt-2 text-3xl font-semibold text-ink">{{ stats.total_conversations }}</div>
                    </div>
                    <div class="surface-card p-4 sm:p-6">
                        <div class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.needs_attention') }}</div>
                        <div class="mt-2 text-3xl font-semibold text-warn">{{ stats.needs_attention }}</div>
                    </div>
                    <div class="surface-card p-4 sm:p-6">
                        <div class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.total_leads') }}</div>
                        <div class="mt-2 text-3xl font-semibold text-ink">{{ stats.total_leads }}</div>
                    </div>
                    <div class="surface-card p-4 sm:p-6">
                        <div class="text-xs uppercase tracking-wide text-muted">{{ t('dashboard.new_leads') }}</div>
                        <div class="mt-2 text-3xl font-semibold text-good">{{ stats.new_leads }}</div>
                    </div>
                </div>

                <div class="surface-card">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-ink">{{ t('dashboard.recent') }}</h3>

                        <div v-if="conversations.data.length === 0" class="py-10 text-center text-muted">
                            {{ t('dashboard.empty') }}
                        </div>

                        <div v-else class="mt-4 space-y-3">
                            <Link
                                v-for="conversation in conversations.data"
                                :key="conversation.id"
                                :href="locRoute('admin.conversations.show', conversation.id)"
                                class="soft-ring block rounded-xl border border-line p-4"
                            >
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-semibold text-ink">
                                                {{ conversation.telegram_first_name || t('dashboard.unnamed') }}
                                                {{ conversation.telegram_last_name || '' }}
                                            </h4>
                                            <span v-if="conversation.telegram_username" class="text-sm text-muted">
                                                @{{ conversation.telegram_username }}
                                            </span>
                                            <span
                                                v-if="conversation.needs_attention"
                                                class="rounded-full bg-warn/15 px-2 py-0.5 text-xs font-medium text-warn"
                                            >
                                                {{ t('dashboard.needs_attention') }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-muted">
                                            {{ t('dashboard.messages') }}: {{ conversation.messages_count }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-sm text-muted">
                                        {{ formatDate(conversation.last_message_at || conversation.created_at) }}
                                    </div>
                                </div>
                                <div v-if="conversation.latest_message" class="mt-2 line-clamp-2 text-sm text-muted">
                                    <span class="font-medium text-ink">{{ roleLabel(conversation.latest_message.role) }}:</span>
                                    {{ conversation.latest_message.content }}
                                </div>
                            </Link>
                        </div>

                        <div v-if="conversations.links && conversations.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
                            <Link
                                v-for="(link, index) in conversations.links"
                                :key="index"
                                :href="link.url || locRoute('admin.dashboard')"
                                :class="[
                                    'rounded-lg border px-3 py-2 text-sm',
                                    link.active ? 'border-accent bg-accent text-accent-fg' : 'border-line bg-surface text-ink',
                                    !link.url && 'pointer-events-none opacity-50',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
