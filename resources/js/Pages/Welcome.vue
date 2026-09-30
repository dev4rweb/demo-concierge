<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    botUsername: String,
});
</script>

<template>
    <Head title="AI Concierge Demo" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-5xl font-bold text-gray-900 mb-4">
                        AI Concierge Demo
                    </h1>
                    <p class="text-xl text-gray-700 mb-2">
                        Демонстрация AI-консьержа для бизнеса
                    </p>
                    <p class="text-lg text-gray-600">
                        Demo of AI Concierge for Business
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">
                        О проекте / About
                    </h2>
                    
                    <div class="space-y-4 text-gray-700">
                        <p>
                            <strong class="text-gray-900">RU:</strong> Интеллектуальный помощник для общения с клиентами через Telegram. 
                            AI-бот консультирует посетителей, отвечает на вопросы и собирает контактную информацию. 
                            Администратор управляет всем через веб-панель на Laravel + Vue.js.
                        </p>
                        
                        <p>
                            <strong class="text-gray-900">EN:</strong> Intelligent assistant for customer communication via Telegram. 
                            AI bot consults visitors, answers questions and collects contact information. 
                            Administrator manages everything through a web panel built with Laravel + Vue.js.
                        </p>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            Технологии / Tech Stack
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Laravel</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Vue 3</span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">Inertia.js</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Tailwind CSS</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Telegram Bot API</span>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">OpenRouter AI</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">MySQL</span>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <a 
                        v-if="botUsername"
                        :href="`https://t.me/${botUsername}`" 
                        target="_blank"
                        class="block bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg p-8 text-center transition-all transform hover:scale-105"
                    >
                        <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.717-1.958 9.112-2.37 11.069-.174.829-.516 1.105-.847 1.133-.719.066-1.265-.475-1.961-.931-1.087-.715-1.702-1.159-2.755-1.856-1.219-.806-.428-1.25.266-1.975.182-.189 3.337-3.057 3.399-3.317.008-.033.016-.155-.058-.22-.074-.066-.183-.043-.262-.026-.112.024-1.898 1.205-5.362 3.539-.508.349-.968.518-1.379.506-.454-.013-1.326-.257-1.975-.468-.796-.26-1.427-.398-1.372-.84.029-.23.348-.465.957-.707 3.748-1.635 6.248-2.716 7.499-3.245 3.569-1.488 4.31-1.747 4.793-1.754.106-.002.344.024.499.149.131.105.167.246.184.346.017.1.038.328.021.506z"/>
                        </svg>
                        <h3 class="text-2xl font-bold mb-2">Открыть бота</h3>
                        <p class="text-blue-100">Open Telegram Bot</p>
                    </a>

                    <Link 
                        :href="route('login')"
                        class="block bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg p-8 text-center transition-all transform hover:scale-105"
                    >
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-2xl font-bold mb-2">Админ-панель</h3>
                        <p class="text-indigo-100">Admin Panel</p>
                    </Link>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 text-center text-gray-600">
                    <p class="mb-2">
                        <strong>Demo credentials:</strong> admin@demo.local / demo123456
                    </p>
                    <p class="text-sm">
                        GitHub: <a href="https://github.com/dev4rweb/demo-concierge" class="text-blue-600 hover:underline" target="_blank">dev4rweb/demo-concierge</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
