User-agent: *
Disallow: /cgi-bin
Disallow: /uploads/themes
User-agent: Mediapartners-Google
Allow: /
User-agent: Adsbot-Google
Allow: /
User-agent: Googlebot-Image
Allow: /
User-agent: Googlebot-Mobile
Allow: /
Sitemap: <?= $this->Url->build('/', true); ?>sitemap.xml