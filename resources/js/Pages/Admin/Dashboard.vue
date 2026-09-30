<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    conversations: Object,
    stats: Object,
});

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
    <Head title="Админ-панель" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Админ-панель</h2>
                <div class="flex gap-4">
                    <Link :href="route('admin.leads.index')" class="text-blue-600 hover:text-blue-800">
                        Лиды
                    </Link>
                    <Link :href="route('admin.knowledge-base.index')" class="text-blue-600 hover:text-blue-800">
                        База знаний
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-600 text-sm mb-2">Всего диалогов</div>
                        <div class="text-3xl font-bold text-gray-900">{{ stats.total_conversations }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-600 text-sm mb-2">Требует внимания</div>
                        <div class="text-3xl font-bold text-orange-600">{{ stats.needs_attention }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-600 text-sm mb-2">Всего лидов</div>
                        <div class="text-3xl font-bold text-gray-900">{{ stats.total_leads }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-600 text-sm mb-2">Новые лиды</div>
                        <div class="text-3xl font-bold text-green-600">{{ stats.new_leads }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Последние диалоги</h3>
                        
                        <div v-if="conversations.data.length === 0" class="text-gray-600 text-center py-8">
                            Диалогов пока нет
                        </div>

                        <div v-else class="space-y-4">
                            <Link 
                                v-for="conversation in conversations.data" 
                                :key="conversation.id"
                                :href="route('admin.conversations.show', conversation.id)"
                                class="block border border-gray-200 rounded-lg p-4 hover:border-blue-400 hover:shadow-md transition-all"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-semibold text-gray-900">
                                                {{ conversation.telegram_first_name || 'Пользователь' }}
                                                {{ conversation.telegram_last_name || '' }}
                                            </h4>
                                            <span v-if="conversation.telegram_username" class="text-gray-500 text-sm">
                                                @{{ conversation.telegram_username }}
                                            </span>
                                            <span v-if="conversation.needs_attention" class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                                Требует внимания
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Сообщений: {{ conversation.messages_count }}
                                        </p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500">
                                        {{ formatDate(conversation.last_message_at || conversation.created_at) }}
                                    </div>
                                </div>
                                <div v-if="conversation.latest_message" class="text-sm text-gray-600 line-clamp-2">
                                    <span class="font-medium">
                                        {{ conversation.latest_message.role === 'user' ? 'Клиент' : 'Бот' }}:
                                    </span>
                                    {{ conversation.latest_message.content }}
                                </div>
                            </Link>
                        </div>

                        <div v-if="conversations.links && conversations.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link 
                                v-for="(link, index) in conversations.links" 
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
