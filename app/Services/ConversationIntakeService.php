<?php

namespace App\Services;

class ConversationIntakeService
{
    /**
     * @var list<string>
     */
    private const HUMAN_KEYWORDS = [
        'человек', 'администратор', 'менеджер', 'оператор', 'живой',
        'human', 'manager', 'admin', 'administrator', 'operator', 'agent',
        'person', 'staff', 'someone', 'real person', 'talk to a person',
        'speak to a human', 'speak to someone', 'talk to someone',
        'contact a manager', 'call me back',
        'persona', 'gerente', 'administrador', 'humano', 'hablar con',
        'personne', 'responsable', 'humain', 'parler à',
        'pessoa', 'atendente', 'falar com',
        '人工', '经理', '管理员', '客服', '转人工',
        'إنسان', 'مدير', 'مسؤول', 'موظف', 'تحدث مع',
    ];

    /**
     * @var list<string>
     */
    private const NAME_PREFIXES = [
        '(?:меня зовут|мо[её] имя|зовут меня)\s+',
        '(?:my name is|i am|i\'m|this is)\s+',
        '(?:je m\'appelle|je suis)\s+',
        '(?:me llamo|mi nombre es|soy)\s+',
        '(?:meu nome [eé]|eu sou)\s+',
        '(?:ich hei(?:ß|ss)e|ich bin)\s+',
        '(?:mi chiamo)\s+',
        '(?:اسمي|أنا)\s+',
        '(?:我叫|我是)\s*',
        '(?:name|имя|nome|nom|nombre)\s*[:\-–]\s*',
    ];

    public function detectLanguage(string $text, ?string $telegramLanguageCode = null): string
    {
        $trimmed = trim($text);

        if ($trimmed !== '' && strtolower($trimmed) !== '/start') {
            if (preg_match('/[\x{0600}-\x{06FF}]/u', $trimmed)) {
                return 'ar';
            }
            if (preg_match('/[\x{4E00}-\x{9FFF}]/u', $trimmed)) {
                return 'zh';
            }
            if (preg_match('/[àâçéèêëîïôùûüÿœæ]/iu', $trimmed) && preg_match('/\b(je|bonjour|merci|besoin|prix|site)\b/iu', $trimmed)) {
                return 'fr';
            }
            if (preg_match('/[áéíóúñ¿¡]/iu', $trimmed) && preg_match('/\b(hola|necesito|precio|gracias|web)\b/iu', $trimmed)) {
                return 'es';
            }
            if (preg_match('/[ãõçáéíóú]/iu', $trimmed) && preg_match('/\b(olá|preciso|obrigado|preço|site)\b/iu', $trimmed)) {
                return 'pt';
            }
            if (preg_match('/[а-яёіїєґ]/iu', $trimmed)) {
                return 'ru';
            }
            if (preg_match('/[a-z]/i', $trimmed)) {
                return 'en';
            }
        }

        $code = strtolower(substr((string) $telegramLanguageCode, 0, 2));
        $known = ['en', 'zh', 'fr', 'es', 'ar', 'pt', 'ru'];

        return in_array($code, $known, true) ? $code : 'en';
    }

    public function mentionsHuman(string $text): bool
    {
        $normalized = mb_strtolower($text);

        foreach (self::HUMAN_KEYWORDS as $keyword) {
            if ($keyword === '') {
                continue;
            }

            if (preg_match('/\b'.preg_quote(mb_strtolower($keyword), '/').'\b/u', $normalized)
                || mb_strpos($normalized, mb_strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{name: ?string, email: ?string, phone: ?string, telegram_contact: ?string, needs: ?string}
     */
    public function extractLeadFields(string $message): array
    {
        return [
            'name' => $this->extractName($message),
            'email' => $this->extractEmail($message),
            'phone' => $this->extractPhone($message),
            'telegram_contact' => $this->extractTelegram($message),
            'needs' => $this->extractNeeds($message),
        ];
    }

    /**
     * @param  array{name: ?string, email: ?string, phone: ?string, telegram_contact: ?string, needs: ?string}  $fields
     */
    public function hasContactData(array $fields): bool
    {
        return filled($fields['name'] ?? null)
            || filled($fields['email'] ?? null)
            || filled($fields['phone'] ?? null)
            || filled($fields['telegram_contact'] ?? null);
    }

    public function welcomeMessage(string $language): string
    {
        return match ($language) {
            'ru' => "Здравствуйте! 👋\n\nЯ демо AI-консьержа для отелей и сервисного бизнеса.\n\nСпросите про часы работы, цены, услуги или как добраться.\n\nНужен человек? Напишите manager или человек — передам запрос специалисту.",
            'zh' => "您好！👋\n\n我是面向酒店及服务行业的 AI 礼宾演示助手。\n\n您可以询问营业时间、价格、服务或路线。\n\n需要人工？发送 manager 或 человек，我会为您转接真人。",
            'fr' => "Bonjour ! 👋\n\nJe suis la démo du concierge IA pour les hôtels et les entreprises de services.\n\nDemandez les horaires, les prix, les services ou l’itinéraire.\n\nBesoin d’une personne ? Tapez manager ou человек et je transmettrai à un humain.",
            'es' => "¡Hola! 👋\n\nSoy la demo del conserje de IA para hoteles y negocios de servicios.\n\nPregunte por horarios, precios, servicios o cómo llegar.\n\n¿Necesita a una persona? Escriba manager o человек y le pasaré con un humano.",
            'ar' => "مرحباً! 👋\n\nأنا عرض تجريبي لكونسيرج الذكاء الاصطناعي للفنادق وشركات الخدمات.\n\nاسأل عن ساعات العمل أو الأسعار أو الخدمات أو الاتجاهات.\n\nتحتاج شخصاً؟ اكتب manager أو человек وسأحوّلك إلى موظف بشري.",
            'pt' => "Olá! 👋\n\nSou a demonstração do concierge de IA para hotéis e negócios de serviços.\n\nPergunte sobre horários, preços, serviços ou indicações.\n\nPrecisa de uma pessoa? Escreva manager ou человек e eu encaminho para um humano.",
            default => "Hello! 👋\n\nI'm an AI concierge demo for hotels and service businesses.\n\nAsk me about hours, prices, services, or directions.\n\nNeed a person? Type manager or человек and I'll connect you with a human.",
        };
    }

    /**
     * @return array{inline_keyboard: list<list<array{text: string, url: string}>>}
     */
    public function welcomeKeyboard(string $language): array
    {
        [$demoLabel, $adminLabel] = match ($language) {
            'ru' => ['Открыть демо', 'Админ-панель'],
            'zh' => ['打开演示站点', '管理面板'],
            'fr' => ['Ouvrir la démo', 'Panneau admin'],
            'es' => ['Abrir la demo', 'Panel de admin'],
            'ar' => ['فتح الموقع التجريبي', 'لوحة التحكم'],
            'pt' => ['Abrir a demo', 'Painel admin'],
            default => ['Open demo site', 'Admin panel'],
        };

        return [
            'inline_keyboard' => [
                [
                    ['text' => $demoLabel, 'url' => $this->localizedDemoUrl('/', $language)],
                    ['text' => $adminLabel, 'url' => $this->localizedDemoUrl('/login', $language)],
                ],
                [
                    ['text' => 'dev4rweb.com', 'url' => 'https://dev4rweb.com'],
                ],
            ],
        ];
    }

    public function escalationMessage(string $language): string
    {
        return match ($language) {
            'ru' => 'Понял! Ваш запрос передан администратору. С вами свяжутся в ближайшее время. Можете оставить свои контактные данные для связи.',
            'zh' => '已为您转接管理员，我们会尽快联系您。您也可以留下联系方式。',
            'fr' => 'Compris ! Votre demande a été transmise à un administrateur. Nous vous recontacterons bientôt. Vous pouvez laisser vos coordonnées.',
            'es' => 'Entendido. Su solicitud se ha enviado a un administrador. Le contactaremos pronto. Puede dejar sus datos de contacto.',
            'ar' => 'تم تحويل طلبك إلى المسؤول. سنتواصل معك قريباً. يمكنك ترك بيانات التواصل.',
            'pt' => 'Percebido! O seu pedido foi enviado a um administrador. Entraremos em contacto em breve. Pode deixar os seus dados.',
            default => 'Understood. Your request has been passed to an administrator. We will contact you shortly. You can leave your contact details.',
        };
    }

    public function fallbackMessage(string $language): string
    {
        return match ($language) {
            'ru' => 'Извините, произошла ошибка. Попробуйте позже или попросите связаться с администратором.',
            'zh' => '抱歉，出现了错误。请稍后再试，或要求转接管理员。',
            'fr' => 'Désolé, une erreur s’est produite. Réessayez plus tard ou demandez un administrateur.',
            'es' => 'Lo sentimos, ha ocurrido un error. Inténtelo más tarde o pida hablar con un administrador.',
            'ar' => 'عذراً، حدث خطأ. حاول لاحقاً أو اطلب التحدث مع مسؤول.',
            'pt' => 'Pedimos desculpa, ocorreu um erro. Tente mais tarde ou peça um administrador.',
            default => 'Sorry, something went wrong. Please try again later or ask to speak with an administrator.',
        };
    }

    private function extractName(string $message): ?string
    {
        foreach (self::NAME_PREFIXES as $prefix) {
            if (preg_match('#'.$prefix.'([^\n,.;:|/]+)#iu', $message, $matches)) {
                $name = trim($matches[1], " \t\n\r\0\x0B\"'«»");
                $name = preg_replace('/\s+/', ' ', $name) ?? $name;
                $name = preg_replace('/\s+(?:and|и|et|y|e)\s+.*/iu', '', $name) ?? $name;

                if ($this->isPlausibleName($name)) {
                    return $name;
                }
            }
        }

        return null;
    }

    private function extractEmail(string $message): ?string
    {
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $message, $matches)) {
            return strtolower($matches[0]);
        }

        return null;
    }

    private function extractPhone(string $message): ?string
    {
        if (preg_match('/(?<!\w)(\+?\d[\d\s\-\(\)]{7,}\d)/u', $message, $matches)) {
            $phone = trim($matches[1]);
            $digits = preg_replace('/\D+/', '', $phone) ?? '';

            if (strlen($digits) >= 10) {
                return $phone;
            }
        }

        return null;
    }

    private function extractTelegram(string $message): ?string
    {
        if (preg_match('/(?<!\w)@([A-Za-z0-9_]{5,32})/', $message, $matches)) {
            return '@'.$matches[1];
        }

        return null;
    }

    private function extractNeeds(string $message): ?string
    {
        if (preg_match('/(?:нужен|нужна|нужно|i need|we need|necesito|j\'ai besoin|preciso|需要)\s+(.+)/iu', $message, $matches)) {
            $needs = trim($matches[1], " \t\n\r\0\x0B.,;");

            return mb_strlen($needs) >= 4 ? $needs : null;
        }

        return null;
    }

    private function isPlausibleName(string $name): bool
    {
        if ($name === '' || mb_strlen($name) > 80) {
            return false;
        }

        if (preg_match('/@|\d{3,}/', $name)) {
            return false;
        }

        return (bool) preg_match('/^[\p{L}][\p{L}\s\'\-\.]+$/u', $name);
    }

    private function localizedDemoUrl(string $path, string $language): string
    {
        $base = 'https://demo-concierge.dev4rweb.com';
        $supported = ['en', 'zh', 'fr', 'es', 'ar', 'pt', 'ru'];
        $locale = in_array($language, $supported, true) ? $language : 'en';
        $normalized = $path === '' ? '/' : $path;

        if ($locale === 'en') {
            return $normalized === '/' ? $base : $base.$normalized;
        }

        return $normalized === '/' ? $base.'/'.$locale : $base.'/'.$locale.$normalized;
    }
}
