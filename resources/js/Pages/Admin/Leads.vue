<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    leads: Object,
});

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
    editForm.patch(route('admin.leads.update', leadId), {
        onSuccess: () => {
            editingLead.value = null;
            editForm.reset();
        },
    });
};

const statusLabels = {
    new: 'Новый',
    contacted: 'Связались',
    converted: 'Конвертирован',
    lost: 'Потерян',
};

const statusColors = {
    new: 'bg-green-100 text-green-800',
    contacted: 'bg-blue-100 text-blue-800',
    converted: 'bg-purple-100 text-purple-800',
    lost: 'bg-gray-100 text-gray-800',
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Лиды" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Лиды</h2>
                <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-800">
                    ← К панели
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="leads.data.length === 0" class="text-gray-600 text-center py-8">
                            Лидов пока нет
                        </div>

                        <div v-else class="space-y-4">
                            <div 
                                v-for="lead in leads.data" 
                                :key="lead.id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div v-if="editingLead === lead.id">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                                            <input 
                                                v-model="editForm.name"
                                                type="text" 
                                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                                            <input 
                                                v-model="editForm.phone"
                                                type="text" 
                                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                            <input 
                                                v-model="editForm.email"
                                                type="email" 
                                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                                            <select 
                                                v-model="editForm.status"
                                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                            >
                                                <option value="new">Новый</option>
                                                <option value="contacted">Связались</option>
                                                <option value="converted">Конвертирован</option>
                                                <option value="lost">Потерян</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Потребность</label>
                                            <textarea 
                                                v-model="editForm.needs"
                                                rows="2"
                                                class="w-full border-gray-300 rounded-lg shadow-sm"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button 
                                            @click="saveEdit(lead.id)"
                                            :disabled="editForm.processing"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50"
                                        >
                                            Сохранить
                                        </button>
                                        <button 
                                            @click="cancelEdit"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg"
                                        >
                                            Отмена
                                        </button>
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h4 class="font-semibold text-gray-900">
                                                    {{ lead.name || 'Без имени' }}
                                                </h4>
                                                <span :class="['px-2 py-1 text-xs rounded-full', statusColors[lead.status]]">
                                                    {{ statusLabels[lead.status] }}
                                                </span>
                                            </div>
                                            <div class="space-y-1 text-sm text-gray-600">
                                                <div v-if="lead.phone">Телефон: {{ lead.phone }}</div>
                                                <div v-if="lead.email">Email: {{ lead.email }}</div>
                                                <div v-if="lead.telegram_contact">Telegram: {{ lead.telegram_contact }}</div>
                                                <div v-if="lead.needs" class="mt-2">
                                                    <span class="font-medium">Потребность:</span> {{ lead.needs }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="text-sm text-gray-500 mb-2">
                                                {{ formatDate(lead.created_at) }}
                                            </div>
                                            <button 
                                                @click="startEdit(lead)"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                            >
                                                Редактировать
                                            </button>
                                            <Link 
                                                v-if="lead.conversation"
                                                :href="route('admin.conversations.show', lead.conversation.id)"
                                                class="block text-blue-600 hover:text-blue-800 text-sm font-medium mt-1"
                                            >
                                                → Диалог
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="leads.links && leads.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link 
                                v-for="(link, index) in leads.links" 
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 border rounded-lg',
                                    link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
