<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from '@/composables/useI18n';

defineProps({
    items: Object,
});

const { t, locRoute } = useI18n();
const showAddForm = ref(false);
const editingItem = ref(null);

const addForm = useForm({
    category: '',
    question: '',
    answer: '',
    is_active: true,
    sort_order: 0,
});

const editForm = useForm({
    category: '',
    question: '',
    answer: '',
    is_active: true,
    sort_order: 0,
});

const submitAdd = () => {
    addForm.post(locRoute('admin.knowledge-base.store'), {
        onSuccess: () => {
            addForm.reset();
            showAddForm.value = false;
        },
    });
};

const startEdit = (item) => {
    editingItem.value = item.id;
    editForm.category = item.category || '';
    editForm.question = item.question;
    editForm.answer = item.answer;
    editForm.is_active = item.is_active;
    editForm.sort_order = item.sort_order;
};

const saveEdit = (itemId) => {
    editForm.patch(locRoute('admin.knowledge-base.update', itemId), {
        onSuccess: () => {
            editingItem.value = null;
            editForm.reset();
        },
    });
};

const deleteItem = (itemId) => {
    if (confirm(t('kb.confirm_delete'))) {
        router.delete(locRoute('admin.knowledge-base.destroy', itemId));
    }
};

const cancelEdit = () => {
    editingItem.value = null;
    editForm.reset();
};
</script>

<template>
    <Head :title="t('kb.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-ink">{{ t('kb.title') }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
                <div class="surface-card p-4 sm:p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-semibold text-ink">{{ t('kb.manage') }}</h3>
                        <button
                            type="button"
                            class="rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-accent-fg"
                            @click="showAddForm = !showAddForm"
                        >
                            {{ showAddForm ? t('common.cancel') : t('kb.add') }}
                        </button>
                    </div>

                    <form v-if="showAddForm" class="rounded-xl border border-line p-4" @submit.prevent="submitAdd">
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium">{{ t('kb.category') }}</label>
                                <input v-model="addForm.category" type="text" class="w-full rounded-xl border-line bg-surface text-ink" :placeholder="t('kb.category_ph')" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">{{ t('kb.order') }}</label>
                                <input v-model.number="addForm.sort_order" type="number" class="w-full rounded-xl border-line bg-surface text-ink" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-medium">{{ t('kb.question') }} *</label>
                                <input v-model="addForm.question" type="text" required class="w-full rounded-xl border-line bg-surface text-ink" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-medium">{{ t('kb.answer') }} *</label>
                                <textarea v-model="addForm.answer" rows="4" required class="w-full rounded-xl border-line bg-surface text-ink" />
                            </div>
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="addForm.is_active" type="checkbox" class="rounded border-line text-accent" />
                                {{ t('kb.active') }}
                            </label>
                        </div>
                        <button type="submit" :disabled="addForm.processing" class="rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-accent-fg disabled:opacity-50">
                            {{ addForm.processing ? t('common.saving') : t('common.save') }}
                        </button>
                    </form>
                </div>

                <div class="surface-card p-4 sm:p-6">
                    <div v-if="items.data.length === 0" class="py-10 text-center text-muted">
                        {{ t('kb.empty') }}
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="item in items.data" :key="item.id" class="rounded-xl border border-line p-4">
                            <form v-if="editingItem === item.id" @submit.prevent="saveEdit(item.id)">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('kb.category') }}</label>
                                        <input v-model="editForm.category" type="text" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium">{{ t('kb.order') }}</label>
                                        <input v-model.number="editForm.sort_order" type="number" class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium">{{ t('kb.question') }} *</label>
                                        <input v-model="editForm.question" type="text" required class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium">{{ t('kb.answer') }} *</label>
                                        <textarea v-model="editForm.answer" rows="4" required class="w-full rounded-xl border-line bg-surface text-ink" />
                                    </div>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input v-model="editForm.is_active" type="checkbox" class="rounded border-line text-accent" />
                                        {{ t('kb.active') }}
                                    </label>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" :disabled="editForm.processing" class="rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-accent-fg disabled:opacity-50">
                                        {{ t('common.save') }}
                                    </button>
                                    <button type="button" class="rounded-xl border border-line px-4 py-2 text-sm" @click="cancelEdit">
                                        {{ t('common.cancel') }}
                                    </button>
                                </div>
                            </form>

                            <div v-else class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                                <div class="min-w-0">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span v-if="item.category" class="rounded bg-surface-2 px-2 py-0.5 text-xs text-muted">{{ item.category }}</span>
                                        <span v-if="!item.is_active" class="rounded bg-danger/15 px-2 py-0.5 text-xs text-danger">{{ t('kb.inactive') }}</span>
                                        <span class="text-xs text-muted">{{ t('kb.order') }}: {{ item.sort_order }}</span>
                                    </div>
                                    <h4 class="mb-2 font-semibold text-ink">{{ item.question }}</h4>
                                    <p class="whitespace-pre-wrap text-muted">{{ item.answer }}</p>
                                </div>
                                <div class="flex shrink-0 gap-3 sm:flex-col">
                                    <button type="button" class="text-sm font-medium text-accent" @click="startEdit(item)">{{ t('common.edit') }}</button>
                                    <button type="button" class="text-sm font-medium text-danger" @click="deleteItem(item.id)">{{ t('common.delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="items.links && items.links.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
                        <Link
                            v-for="(link, index) in items.links"
                            :key="index"
                            :href="link.url || locRoute('admin.knowledge-base.index')"
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
