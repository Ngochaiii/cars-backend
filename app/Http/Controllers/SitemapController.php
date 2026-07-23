<?php

namespace App\Http\Controllers;

use App\Support\Sitemap;
use Illuminate\Http\Response;

class SitemapController
{
    public function __invoke(Sitemap $sitemap): Response
    {
        return response($sitemap->toXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
