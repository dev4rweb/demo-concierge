# AI Concierge Demo / Демо AI-консьержа

[English](#english) | [Русский](#russian)

---

## English

### About

AI Concierge for business websites - a demonstration portfolio project showing integration between Telegram Bot, AI (via OpenRouter), and Laravel+Vue.js admin panel.

**Live Demo:** [https://demo-concierge.dev4rweb.com](https://demo-concierge.dev4rweb.com)

### Features

- **Telegram Bot** 
  - Welcome messages and conversation management
  - AI-powered responses using OpenRouter API
  - Lead information collection (name, contacts, needs)
  - Escalation to human admin
  
- **Admin Panel (Laravel + Vue.js + Inertia.js)**
  - Dashboard with conversation overview
  - Detailed conversation view and reply functionality
  - Lead management with status tracking
  - Knowledge base CRUD for AI training
  - Simple authentication

- **Public Landing Page**
  - Project description (RU/EN)
  - Links to Telegram bot and admin panel
  
### Tech Stack

- **Backend:** Laravel 11, MySQL
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS, Vite
- **AI:** OpenRouter API (OpenAI-compatible)
- **Messaging:** Telegram Bot API
- **Deployment:** Hostinger shared hosting compatible

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Telegram Bot Token ([create via @BotFather](https://t.me/BotFather))
- OpenRouter API Key ([get here](https://openrouter.ai/))

### Local Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/dev4rweb/demo-concierge.git
   cd demo-concierge
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure .env**
   ```env
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   TELEGRAM_BOT_TOKEN=your_bot_token
   TELEGRAM_BOT_USERNAME=your_bot_username
   TELEGRAM_WEBHOOK_SECRET=optional_random_string
   
   OPENROUTER_API_KEY=your_openrouter_key
   OPENROUTER_MODEL=deepseek/deepseek-chat-v3-0324:free
   
   DEMO_ADMIN_EMAIL=admin@demo.local
   DEMO_ADMIN_PASSWORD=demo123456
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   npm run dev  # In another terminal
   ```

8. **Set Telegram webhook** (for production) or use polling for local development
   ```bash
   # Without secret (basic)
   curl -X POST https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook \
     -d "url=https://yourdomain.com/telegram/webhook"
   
   # With secret token (recommended for production)
   curl -X POST https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook \
     -d "url=https://yourdomain.com/telegram/webhook" \
     -d "secret_token=<YOUR_WEBHOOK_SECRET>"
   ```
   **Note:** If using webhook secret, set `TELEGRAM_WEBHOOK_SECRET` in `.env` to the same value.

### Hostinger Deployment

1. **Upload files** via FTP/SFTP to your hosting
2. **Set document root** to `/public` directory
3. **Import database** and run migrations
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
4. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```
5. **Configure webhook**
   ```bash
   # Basic webhook
   curl -X POST https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook \
     -d "url=https://demo-concierge.dev4rweb.com/telegram/webhook"
   
   # With secret token (recommended)
   curl -X POST https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook \
     -d "url=https://demo-concierge.dev4rweb.com/telegram/webhook" \
     -d "secret_token=<YOUR_WEBHOOK_SECRET>"
   ```
   Set the same secret in `.env` as `TELEGRAM_WEBHOOK_SECRET`

### Admin Access

- **URL:** `/login`
- **Email:** admin@demo.local (or from .env)
- **Password:** demo123456 (or from .env)

### Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/              # Admin panel controllers
│   ├── Auth/               # Authentication controllers
│   ├── TelegramWebhookController.php
│   └── WelcomeController.php
├── Models/                 # Eloquent models
└── Services/              # Business logic (Telegram, OpenRouter)

resources/js/Pages/
├── Admin/                 # Admin Vue components
├── Auth/                  # Auth pages
└── Welcome.vue           # Landing page

database/
├── migrations/           # Database schema
└── seeders/             # Demo data seeders
```

### Telegram Polling (Local Development Alternative)

For local development without webhook, create `routes/console.php` command:

```php
Artisan::command('telegram:poll', function () {
    $service = app(\App\Services\TelegramService::class);
    // Implement long-polling logic
})->purpose('Poll Telegram updates');
```

### License

Open source for portfolio demonstration purposes.

---

## Russian

### О проекте

AI-консьерж для бизнес-сайтов - демонстрационный портфолио-проект, показывающий интеграцию Telegram-бота, AI (через OpenRouter) и админ-панели на Laravel+Vue.js.

**Демо:** [https://demo-concierge.dev4rweb.com](https://demo-concierge.dev4rweb.com)

### Возможности

- **Telegram-бот**
  - Приветственные сообщения и управление диалогами
  - AI-ответы через OpenRouter API
  - Сбор информации о лидах (имя, контакты, потребности)
  - Эскалация к администратору

- **Админ-панель (Laravel + Vue.js + Inertia.js)**
  - Дашборд с обзором диалогов
  - Детальный просмотр диалогов и ответы
  - Управление лидами со статусами
  - CRUD базы знаний для обучения AI
  - Простая аутентификация

- **Публичная посадочная страница**
  - Описание проекта (RU/EN)
  - Ссылки на бота и админ-панель

### Технологии

- **Backend:** Laravel 11, MySQL
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS, Vite
- **AI:** OpenRouter API (совместимый с OpenAI)
- **Сообщения:** Telegram Bot API
- **Деплой:** Совместим с Hostinger shared hosting

### Требования

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Токен Telegram-бота ([создать через @BotFather](https://t.me/BotFather))
- API-ключ OpenRouter ([получить здесь](https://openrouter.ai/))

### Локальная установка

1. **Клонировать репозиторий**
   ```bash
   git clone https://github.com/dev4rweb/demo-concierge.git
   cd demo-concierge
   ```

2. **Установить зависимости**
   ```bash
   composer install
   npm install
   ```

3. **Настроить окружение**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Настроить .env**
   ```env
   DB_DATABASE=ваша_база
   DB_USERNAME=пользователь
   DB_PASSWORD=пароль
   
   TELEGRAM_BOT_TOKEN=токен_бота
   TELEGRAM_BOT_USERNAME=username_бота
   TELEGRAM_WEBHOOK_SECRET=опциональная_случайная_строка
   
   OPENROUTER_API_KEY=ключ_openrouter
   OPENROUTER_MODEL=deepseek/deepseek-chat-v3-0324:free
   
   DEMO_ADMIN_EMAIL=admin@demo.local
   DEMO_ADMIN_PASSWORD=demo123456
   ```

5. **Настроить базу данных**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Собрать ассеты**
   ```bash
   npm run build
   ```

7. **Запустить сервер разработки**
   ```bash
   php artisan serve
   npm run dev  # В другом терминале
   ```

8. **Установить webhook** (для продакшена) или использовать polling для локальной разработки
   ```bash
   # Без секрета (базовый)
   curl -X POST https://api.telegram.org/bot<ВАШ_ТОКЕН>/setWebhook \
     -d "url=https://ваш-домен.com/telegram/webhook"
   
   # С секретным токеном (рекомендуется для продакшена)
   curl -X POST https://api.telegram.org/bot<ВАШ_ТОКЕН>/setWebhook \
     -d "url=https://ваш-домен.com/telegram/webhook" \
     -d "secret_token=<ВАШ_WEBHOOK_SECRET>"
   ```
   **Примечание:** При использовании секрета установите `TELEGRAM_WEBHOOK_SECRET` в `.env` с тем же значением.

### Деплой на Hostinger

1. **Загрузить файлы** через FTP/SFTP
2. **Установить document root** на `/public`
3. **Импортировать БД** и выполнить миграции
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
4. **Установить права**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```
5. **Настроить webhook**
   ```bash
   # Базовый webhook
   curl -X POST https://api.telegram.org/bot<ВАШ_ТОКЕН>/setWebhook \
     -d "url=https://demo-concierge.dev4rweb.com/telegram/webhook"
   
   # С секретным токеном (рекомендуется)
   curl -X POST https://api.telegram.org/bot<ВАШ_ТОКЕН>/setWebhook \
     -d "url=https://demo-concierge.dev4rweb.com/telegram/webhook" \
     -d "secret_token=<ВАШ_WEBHOOK_SECRET>"
   ```
   Установите тот же секрет в `.env` как `TELEGRAM_WEBHOOK_SECRET`

### Доступ в админку

- **URL:** `/login`
- **Email:** admin@demo.local (или из .env)
- **Пароль:** demo123456 (или из .env)

### Структура проекта

```
app/
├── Http/Controllers/
│   ├── Admin/              # Контроллеры админки
│   ├── Auth/               # Контроллеры аутентификации
│   ├── TelegramWebhookController.php
│   └── WelcomeController.php
├── Models/                 # Eloquent модели
└── Services/              # Бизнес-логика (Telegram, OpenRouter)

resources/js/Pages/
├── Admin/                 # Vue-компоненты админки
├── Auth/                  # Страницы аутентификации
└── Welcome.vue           # Посадочная страница

database/
├── migrations/           # Схема БД
└── seeders/             # Сидеры с демо-данными
```

### Webhook URL Path

Путь для webhook: `https://ваш-домен.com/telegram/webhook`

### Лицензия

Open source для демонстрации в портфолио.

---

**Repository:** [https://github.com/dev4rweb/demo-concierge](https://github.com/dev4rweb/demo-concierge)
