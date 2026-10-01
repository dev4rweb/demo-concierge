<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    items: Object,
});

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
    addForm.post(route('admin.knowledge-base.store'), {
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
    editForm.patch(route('admin.knowledge-base.update', itemId), {
        onSuccess: () => {
            editingItem.value = null;
            editForm.reset();
        },
    });
};

const deleteItem = (itemId) => {
    if (confirm('Вы уверены, что хотите удалить эту запись?')) {
        router.delete(route('admin.knowledge-base.destroy', itemId));
    }
};

const cancelEdit = () => {
    editingItem.value = null;
    editForm.reset();
};
</script>

<template>
    <Head title="База знаний" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">База знаний</h2>
                <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-800">
                    ← К панели
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Управление базой знаний</h3>
                            <button 
                                @click="showAddForm = !showAddForm"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                            >
                                {{ showAddForm ? 'Отмена' : '+ Добавить' }}
                            </button>
                        </div>

                        <div v-if="showAddForm" class="border border-gray-200 rounded-lg p-4 mb-4">
                            <form @submit.prevent="submitAdd">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                                        <input 
                                            v-model="addForm.category"
                                            type="text" 
                                            class="w-full border-gray-300 rounded-lg shadow-sm"
                                            placeholder="Например: Услуги"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Порядок</label>
                                        <input 
                                            v-model.number="addForm.sort_order"
                                            type="number" 
                                            class="w-full border-gray-300 rounded-lg shadow-sm"
                                        />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Вопрос *</label>
                                        <input 
                                            v-model="addForm.question"
                                            type="text" 
                                            class="w-full border-gray-300 rounded-lg shadow-sm"
                                            required
                                        />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ответ *</label>
                                        <textarea 
                                            v-model="addForm.answer"
                                            rows="4"
                                            class="w-full border-gray-300 rounded-lg shadow-sm"
                                            required
                                        ></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="flex items-center">
                                            <input 
                                                v-model="addForm.is_active"
                                                type="checkbox" 
                                                class="rounded border-gray-300 text-blue-600 shadow-sm"
                                            />
                                            <span class="ml-2 text-sm text-gray-700">Активно</span>
                                        </label>
                                    </div>
                                </div>
                                <button 
                                    type="submit"
                                    :disabled="addForm.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50"
                                >
                                    {{ addForm.processing ? 'Сохранение...' : 'Сохранить' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="items.data.length === 0" class="text-gray-600 text-center py-8">
                            База знаний пуста
                        </div>

                        <div v-else class="space-y-4">
                            <div 
                                v-for="item in items.data" 
                                :key="item.id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div v-if="editingItem === item.id">
                                    <form @submit.prevent="saveEdit(item.id)">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                                                <input 
                                                    v-model="editForm.category"
                                                    type="text" 
                                                    class="w-full border-gray-300 rounded-lg shadow-sm"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Порядок</label>
                                                <input 
                                                    v-model.number="editForm.sort_order"
                                                    type="number" 
                                                    class="w-full border-gray-300 rounded-lg shadow-sm"
                                                />
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Вопрос *</label>
                                                <input 
                                                    v-model="editForm.question"
                                                    type="text" 
                                                    class="w-full border-gray-300 rounded-lg shadow-sm"
                                                    required
                                                />
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Ответ *</label>
                                                <textarea 
                                                    v-model="editForm.answer"
                                                    rows="4"
                                                    class="w-full border-gray-300 rounded-lg shadow-sm"
                                                    required
                                                ></textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="flex items-center">
                                                    <input 
                                                        v-model="editForm.is_active"
                                                        type="checkbox" 
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm"
                                                    />
                                                    <span class="ml-2 text-sm text-gray-700">Активно</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button 
                                                type="submit"
                                                :disabled="editForm.processing"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50"
                                            >
                                                Сохранить
                                            </button>
                                            <button 
                                                type="button"
                                                @click="cancelEdit"
                                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg"
                                            >
                                                Отмена
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div v-else>
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span v-if="item.category" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                                    {{ item.category }}
                                                </span>
                                                <span v-if="!item.is_active" class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">
                                                    Неактивно
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Порядок: {{ item.sort_order }}
                                                </span>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 mb-2">{{ item.question }}</h4>
                                            <p class="text-gray-700 whitespace-pre-wrap">{{ item.answer }}</p>
                                        </div>
                                        <div class="ml-4 flex flex-col gap-2">
                                            <button 
                                                @click="startEdit(item)"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                            >
                                                Редактировать
                                            </button>
                                            <button 
                                                @click="deleteItem(item.id)"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium"
                                            >
                                                Удалить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="items.links && items.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link 
                                v-for="(link, index) in items.links" 
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
