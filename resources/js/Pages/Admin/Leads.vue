<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';

const props = defineProps({
    leads: Object,
});

const { t, locRoute, localeTag } = useI18n();
const editingLead = ref(null);

const editForm = useForm({
    name: '',
    phone: '',
    email: '',
    telegram_contact: '',
    needs: '',
    status: 'new',
});

const startEdit = (lead) => {
    editingLead.value = lead.id;
    editForm.name = lead.name || '';
    editForm.phone = lead.phone || '';
    editForm.email = lead.email || '';
    editForm.telegram_contact = lead.telegram_contact || '';
    editForm.needs = lead.needs || '';
    editForm.status = lead.status;
};

const cancelEdit = () => {
    editingLead.value = null;
    editForm.reset();
};

const saveEdit = (leadId) => {
    editForm.patch(locRoute('admin.leads.update', leadId), {
        onSuccess: () => {
            editingLead.value = null;
            editForm.reset();
        },
    });
};

const statusColors = {
    new: 'bg-good/15 text-good',
    contacted: 'bg-accent/15 text-accent',
    converted: 'bg-surface-2 text-ink',
    lost: 'bg-line text-muted',
};

const formatDate = (date) => {
    return new Date(date).toLocaleString(localeTag.value, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="t('leads.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-ink">{{ t('leads.title') }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="surface-card p-4 sm:p-6">
                    <div v-if="leads.data.length === 0" class="py-10 text-center text-muted">
                        {{ t('leads.empty') }}
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="lead in leads.data"
                            :key="lead.id"
                            class="rounded-xl border border-line p-4"
                        >
                            <div v-if="editingLead === lead.id">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('common.name') }}</label>
                                        <input v-model="editForm.name" type="text" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('common.phone') }}</label>
                                        <input v-model="editForm.phone" type="text" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('common.email') }}</label>
                                        <input v-model="editForm.email" type="email" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('common.status') }}</label>
                                        <select v-model="editForm.status" class="w-full rounded-xl border-line bg-surface text-ink">
                                            <option value="new">{{ t('leads.statuses.new') }}</option>
                                            <option value="contacted">{{ t('leads.statuses.contacted') }}</option>
                                            <option value="converted">{{ t('leads.statuses.converted') }}</option>
                                            <option value="lost">{{ t('leads.statuses.lost') }}</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium">{{ t('leads.needs') }}</label>
                                        <textarea v-model="editForm.needs" rows="2" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        :disabled="editForm.processing"
                                        class="rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-accent-fg disabled:opacity-50"
                                        @click="saveEdit(lead.id)"
                                    >
                                        {{ t('common.save') }}
                                    </button>
                                    <button type="button" class="rounded-xl border border-line px-4 py-2 text-sm" @click="cancelEdit">
                                        {{ t('common.cancel') }}
                                    </button>
                                </div>
                            </div>

                            <div v-else class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                                <div class="min-w-0">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-ink">{{ lead.name || t('leads.unnamed') }}</h4>
                                        <span :class="['rounded-full px-2 py-0.5 text-xs', statusColors[lead.status]]">
                                            {{ t(`leads.statuses.${lead.status}`) }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-sm text-muted">
                                        <div v-if="lead.phone">{{ t('common.phone') }}: {{ lead.phone }}</div>
                                        <div v-if="lead.email">{{ t('common.email') }}: {{ lead.email }}</div>
                                        <div v-if="lead.telegram_contact">{{ t('leads.telegram') }}: {{ lead.telegram_contact }}</div>
                                        <div v-if="lead.needs" class="mt-2">
                                            <span class="font-medium text-ink">{{ t('leads.needs') }}:</span> {{ lead.needs }}
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-sm sm:text-end">
                                    <div class="mb-2 text-muted">{{ formatDate(lead.created_at) }}</div>
                                    <button type="button" class="font-medium text-accent" @click="startEdit(lead)">
                                        {{ t('common.edit') }}
                                    </button>
                                    <Link
                                        v-if="lead.conversation"
                                        :href="locRoute('admin.conversations.show', lead.conversation.id)"
                                        class="mt-1 block font-medium text-accent"
                                    >
                                        {{ t('leads.open_dialog') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="leads.links && leads.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
                        <Link
                            v-for="(link, index) in leads.links"
                            :key="index"
                            :href="link.url || locRoute('admin.leads.index')"
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
    </AuthenticatedLayout>
</template>
