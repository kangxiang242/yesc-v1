<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class RobotsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $robotsContent = <<<'TXT'
User-agent: *
Disallow: /admin
Disallow: /observer
Disallow: /order/verify
Disallow: /shopping
Disallow: /check
Disallow: /message

Sitemap: {{ site_url }}/sitemap.xml
TXT;

        // 替换 site_url 占位符
        $siteUrl = config('app.url');
        $robotsContent = str_replace('{{ site_url }}', $siteUrl, $robotsContent);

        Config::updateOrCreate(
            ['name' => 'robots'],
            ['content' => $robotsContent]
        );
    }
}