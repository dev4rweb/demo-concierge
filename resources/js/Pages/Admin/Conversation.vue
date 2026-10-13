<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    conversation: Object,
});

const { t, locRoute, localeTag } = useI18n();

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(locRoute('admin.conversations.reply', props.conversation.id), {
        onSuccess: () => form.reset(),
    });
};

const toggleAttention = () => {
    router.post(locRoute('admin.conversations.toggle-attention', props.conversation.id), {}, {
        preserveScroll: true,
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString(localeTag.value, {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const roleLabel = (role) => {
    if (role === 'user') return t('conversation.role_user');
    if (role === 'admin') return t('conversation.role_admin');
    return t('conversation.role_assistant');
};
</script>

<template>
    <Head :title="t('conversation.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <Link :href="locRoute('admin.dashboard')" class="text-sm text-accent hover:underline">
                        ← {{ t('conversation.back') }}
                    </Link>
                    <h2 class="mt-1 truncate text-xl font-semibold text-ink">
                        {{ conversation.telegram_first_name || t('dashboard.unnamed') }}
                        {{ conversation.telegram_last_name || '' }}
                    </h2>
                </div>
                <button
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition"
                    :class="conversation.needs_attention ? 'bg-warn text-accent-fg' : 'border border-line bg-surface text-ink'"
                    @click="toggleAttention"
                >
                    {{ conversation.needs_attention ? t('conversation.unmark') : t('conversation.mark') }}
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6">
                <div class="surface-card p-5">
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <span class="text-muted">{{ t('conversation.telegram_id') }}:</span>
                            <span class="ms-2 font-medium">{{ conversation.telegram_user_id }}</span>
                        </div>
                        <div v-if="conversation.telegram_username">
                            <span class="text-muted">{{ t('conversation.username') }}:</span>
                            <span class="ms-2 font-medium">@{{ conversation.telegram_username }}</span>
                        </div>
                        <div>
                            <span class="text-muted">{{ t('conversation.started') }}:</span>
                            <span class="ms-2 font-medium">{{ formatDate(conversation.created_at) }}</span>
                        </div>
                        <div>
                            <span class="text-muted">{{ t('conversation.lead_status') }}:</span>
                            <span class="ms-2 font-medium">
                                {{ conversation.lead ? t(`leads.statuses.${conversation.lead.status}`) : t('conversation.no_lead') }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="conversation.lead && (conversation.lead.name || conversation.lead.phone || conversation.lead.email)"
                        class="mt-4 border-t border-line pt-4"
                    >
                        <h4 class="mb-2 font-semibold text-ink">{{ t('conversation.contacts') }}</h4>
                        <div class="space-y-1 text-sm">
                            <div v-if="conversation.lead.name">
                                <span class="text-muted">{{ t('common.name') }}:</span>
                                <span class="ms-2 font-medium">{{ conversation.lead.name }}</span>
                            </div>
                            <div v-if="conversation.lead.phone">
                                <span class="text-muted">{{ t('common.phone') }}:</span>
                                <span class="ms-2 font-medium">{{ conversation.lead.phone }}</span>
                            </div>
                            <div v-if="conversation.lead.email">
                                <span class="text-muted">{{ t('common.email') }}:</span>
                                <span class="ms-2 font-medium">{{ conversation.lead.email }}</span>
                            </div>
                            <div v-if="conversation.lead.needs">
                                <span class="text-muted">{{ t('conversation.needs') }}:</span>
                                <span class="ms-2">{{ conversation.lead.needs }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <h3 class="mb-4 text-lg font-semibold text-ink">{{ t('conversation.history') }}</h3>
                    <div class="max-h-[500px] space-y-3 overflow-y-auto">
                        <div
                            v-for="message in conversation.messages"
                            :key="message.id"
                            class="rounded-xl p-4"
                            :class="message.role === 'user' ? 'ms-4 bg-surface-2 sm:ms-10' : 'me-4 bg-accent/10 sm:me-10'"
                        >
                            <div class="mb-2 flex items-start justify-between gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wide text-muted">
                                    {{ roleLabel(message.role) }}
                                </span>
                                <span class="shrink-0 text-xs text-muted">{{ formatDate(message.created_at) }}</span>
                            </div>
                            <p class="whitespace-pre-wrap text-ink">{{ message.content }}</p>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-5">
                    <h3 class="mb-4 text-lg font-semibold text-ink">{{ t('conversation.reply_title') }}</h3>
                    <form @submit.prevent="submit">
                        <textarea
                            v-model="form.message"
                            rows="4"
                            class="w-full rounded-xl border-line bg-surface text-ink shadow-sm focus:border-accent focus:ring-accent"
                            :placeholder="t('conversation.reply_placeholder')"
                            required
                        />
                        <p v-if="form.errors.message" class="mt-1 text-sm text-danger">{{ form.errors.message }}</p>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-4 rounded-xl bg-accent px-5 py-2.5 text-sm font-semibold text-accent-fg disabled:opacity-50"
                        >
                            {{ form.processing ? t('common.sending') : t('common.send') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
