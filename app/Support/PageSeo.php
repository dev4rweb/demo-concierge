<?php

namespace App\Support;

use Illuminate\Http\Request;

class PageSeo
{
    /**
     * @return array{title: string, description: string}
     */
    public static function forRequest(Request $request): array
    {
        $name = self::bareRouteName($request);

        return match ($name) {
            'guide' => [
                'title' => __('app.guide.meta.title'),
                'description' => __('app.guide.meta.description'),
            ],
            'about' => [
                'title' => __('app.about.meta.title'),
                'description' => __('app.about.meta.description'),
            ],
            'login' => [
                'title' => __('app.auth.meta.login_title'),
                'description' => __('app.auth.meta.login_description'),
            ],
            default => [
                'title' => __('app.meta.title'),
                'description' => __('app.meta.description'),
            ],
        };
    }

    public static function unprefixedPath(Request $request): string
    {
        $path = '/'.ltrim($request->path(), '/');
        $supported = implode('|', config('localization.supported', ['en']));
        $stripped = preg_replace('#^/('.$supported.')(?=/|$)#', '', $path) ?? $path;

        return $stripped === '' ? '/' : $stripped;
    }

    /**
     * @return list<array{code: string, url: string}>
     */
    public static function alternateUrls(Request $request): array
    {
        return self::alternatesForPath(self::unprefixedPath($request));
    }

    /**
     * @return list<array{code: string, url: string}>
     */
    public static function alternatesForPath(string $path): array
    {
        $default = config('localization.default', 'en');
        $urls = [];

        foreach (config('localization.supported', []) as $code) {
            $urls[] = [
                'code' => $code,
                'url' => self::localizedUrl($path, $code, $default),
            ];
        }

        return $urls;
    }

    public static function localizedUrl(string $path, string $locale, ?string $default = null): string
    {
        $default ??= config('localization.default', 'en');
        $normalized = $path === '' ? '/' : $path;

        if ($locale === $default) {
            return url($normalized);
        }

        $suffix = $normalized === '/' ? '/'.$locale : '/'.$locale.$normalized;

        return url($suffix);
    }

    public static function bareRouteName(Request $request): string
    {
        $name = (string) $request->route()?->getName();

        if (str_starts_with($name, 'locale.')) {
            return substr($name, 7);
        }

        return $name;
    }
}
