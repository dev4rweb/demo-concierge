<?php

namespace App\Http\Middleware;

use App\Support\PageSeo;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $rtl = config('localization.rtl', ['ar']);
        $hidden = config('localization.hidden_in_selector', []);

        $selectorLocales = collect(config('localization.supported', []))
            ->reject(fn (string $code): bool => in_array($code, $hidden, true))
            ->map(fn (string $code): array => [
                'code' => $code,
                'name' => config("localization.names.$code", $code),
                'flag' => config("localization.flag_codes.$code"),
            ])
            ->values()
            ->all();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => $locale,
            'routeLocale' => $request->route('locale'),
            'dir' => in_array($locale, $rtl, true) ? 'rtl' : 'ltr',
            'translations' => trans('app'),
            'locales' => $selectorLocales,
            'seo' => PageSeo::forRequest($request),
            'demo' => [
                'email' => config('services.demo.admin_email', 'admin@demo.local'),
                'password' => config('services.demo.admin_password', 'demo123456'),
            ],
        ];
    }
}
