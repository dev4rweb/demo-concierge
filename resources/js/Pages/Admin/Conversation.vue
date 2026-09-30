<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    conversation: Object,
});

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(route('admin.conversations.reply', props.conversation.id), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const toggleAttention = () => {
    router.post(route('admin.conversations.toggle-attention', props.conversation.id), {}, {
        preserveScroll: true,
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Диалог" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-800">
                        ← Назад
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ conversation.telegram_first_name || 'Пользователь' }}
                        {{ conversation.telegram_last_name || '' }}
                    </h2>
                </div>
                <button 
                    @click="toggleAttention"
                    :class="[
                        'px-4 py-2 rounded-lg font-medium transition-colors',
                        conversation.needs_attention 
                            ? 'bg-orange-600 text-white hover:bg-orange-700' 
                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    ]"
                >
                    {{ conversation.needs_attention ? 'Снять отметку' : 'Требует внимания' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Telegram ID:</span>
                            <span class="ml-2 font-medium">{{ conversation.telegram_user_id }}</span>
                        </div>
                        <div v-if="conversation.telegram_username">
                            <span class="text-gray-600">Username:</span>
                            <span class="ml-2 font-medium">@{{ conversation.telegram_username }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Начало диалога:</span>
                            <span class="ml-2 font-medium">{{ formatDate(conversation.created_at) }}</span>
                        </div>
                        <div v-if="conversation.lead">
                            <span class="text-gray-600">Статус лида:</span>
                            <span class="ml-2 font-medium">
                                {{ conversation.lead.status === 'new' ? 'Новый' : conversation.lead.status }}
                            </span>
                        </div>
                    </div>

                    <div v-if="conversation.lead && (conversation.lead.name || conversation.lead.phone || conversation.lead.email)" 
                         class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-2">Контактная информация:</h4>
                        <div class="space-y-1 text-sm">
                            <div v-if="conversation.lead.name">
                                <span class="text-gray-600">Имя:</span>
                                <span class="ml-2 font-medium">{{ conversation.lead.name }}</span>
                            </div>
                            <div v-if="conversation.lead.phone">
                                <span class="text-gray-600">Телефон:</span>
                                <span class="ml-2 font-medium">{{ conversation.lead.phone }}</span>
                            </div>
                            <div v-if="conversation.lead.email">
                                <span class="text-gray-600">Email:</span>
                                <span class="ml-2 font-medium">{{ conversation.lead.email }}</span>
                            </div>
                            <div v-if="conversation.lead.needs">
                                <span class="text-gray-600">Потребность:</span>
                                <span class="ml-2">{{ conversation.lead.needs }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">История сообщений</h3>
                        
                        <div class="space-y-4 max-h-[500px] overflow-y-auto">
                            <div 
                                v-for="message in conversation.messages" 
                                :key="message.id"
                                :class="[
                                    'p-4 rounded-lg',
                                    message.role === 'user' ? 'bg-gray-100 ml-8' : 
                                    message.role === 'admin' ? 'bg-blue-100 mr-8' : 
                                    'bg-green-50 mr-8'
                                ]"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-semibold uppercase text-gray-600">
                                        {{ message.role === 'user' ? 'Клиент' : message.role === 'admin' ? 'Администратор' : 'AI Бот' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ formatDate(message.created_at) }}
                                    </span>
                                </div>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ message.content }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ответить пользователю</h3>
                        
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <textarea
                                    v-model="form.message"
                                    rows="4"
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                                    placeholder="Введите сообщение..."
                                    required
                                ></textarea>
                                <div v-if="form.errors.message" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.message }}
                                </div>
                            </div>
                            
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg disabled:opacity-50"
                            >
                                {{ form.processing ? 'Отправка...' : 'Отправить' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
