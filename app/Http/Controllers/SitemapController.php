<?php

namespace App\Http\Controllers;

use App\Support\PageSeo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $paths = ['/', '/about', '/how-to'];
        $entries = [];

        foreach ($paths as $path) {
            $alternates = [];

            foreach (PageSeo::alternatesForPath($path) as $alternate) {
                $alternates[$alternate['code']] = $alternate['url'];
            }

            $alternates['x-default'] = PageSeo::localizedUrl($path, config('localization.default', 'en'));

            foreach ($alternates as $code => $loc) {
                if ($code === 'x-default') {
                    continue;
                }

                $entries[] = [
                    'loc' => $loc,
                    'alternates' => $alternates,
                ];
            }
        }

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
