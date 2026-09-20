<?php echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
@foreach($url['alternates'] ?? [] as $code => $href)
        <xhtml:link rel="alternate" hreflang="{{ \App\Support\Locales::html($code) }}" href="{{ $href }}"/>
@endforeach
        <lastmod>{{ $url['lastmod']?->toAtomString() }}</lastmod>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ number_format($url['priority'], 1) }}</priority>
    </url>
@endforeach
</urlset>
