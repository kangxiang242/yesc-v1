<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Facades\Config;

/**
 * Schema.org JSON-LD 结构化数据生成服务
 * 用于提升 SEO 和 Google AI 摘要的可信度
 */
class SchemaService
{
    /**
     * 生成 Organization + WebSite + SearchAction Schema
     */
    public static function organization(): array
    {
        $siteUrl = Config::get('app.url');
        $siteName = ConfigService::get('site_name') ?: '威而鋼正品購買平台';

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $siteUrl . '/#organization',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $siteUrl . '/static/v2/img/logo.png',
                    ],
                    'sameAs' => [],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $siteUrl . '/#website',
                    'url' => $siteUrl,
                    'name' => $siteName,
                    'publisher' => [
                        '@id' => $siteUrl . '/#organization',
                    ],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $siteUrl . '/search?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];
    }

    /**
     * 生成 BreadcrumbList Schema
     */
    public static function breadcrumb(array $items): array
    {
        $breadcrumbList = [];

        foreach ($items as $index => $item) {
            $breadcrumbList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbList,
        ];
    }

    /**
     * 生成 Article/BlogPosting Schema
     */
    public static function article(Article $article): array
    {
        $siteUrl = Config::get('app.url');
        $articleUrl = $siteUrl . '/' . $article->cate->uri . '/' . $article->id . '.html';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => $articleUrl . '/#article',
            'headline' => $article->title,
            'description' => $article->seo_description ?: $article->brief,
            'image' => [
                '@type' => 'ImageObject',
                'url' => asset_upload($article->img),
            ],
            'datePublished' => $article->release_at->format('Y-m-d\TH:i:s+08:00'),
            'dateModified' => $article->last_updated_at ? $article->last_updated_at->format('Y-m-d\TH:i:s+08:00') : $article->release_at->format('Y-m-d\TH:i:s+08:00'),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author_name ?: '威而鋼資訊團隊',
            ],
            'publisher' => [
                '@id' => $siteUrl . '/#organization',
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $articleUrl,
            ],
        ];

        // 添加审核者信息（如果有）
        if ($article->reviewer_name) {
            $schema['reviewedBy'] = [
                '@type' => 'Person',
                'name' => $article->reviewer_name,
            ];
        }

        return $schema;
    }

    /**
     * 生成 Product Schema
     * 注意：不虚构评分，仅展示真实信息
     */
    public static function product(Product $product): array
    {
        $siteUrl = Config::get('app.url');
        $productUrl = $siteUrl . '/product/' . $product->id;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => $productUrl . '/#product',
            'name' => $product->name,
            'description' => $product->desc ?: $product->name,
            'image' => [
                '@type' => 'ImageObject',
                'url' => asset_upload($product->img),
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Pfizer',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $productUrl,
                'priceCurrency' => 'TWD',
                'price' => $product->price,
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@id' => $siteUrl . '/#organization',
                ],
            ],
        ];

        // 如果有真实评分数据，可以添加（但不要虚构）
        // if ($product->rating && $product->review_count) {
        //     $schema['aggregateRating'] = [
        //         '@type' => 'AggregateRating',
        //         'ratingValue' => $product->rating,
        //         'reviewCount' => $product->review_count,
        //     ];
        // }

        return $schema;
    }

    /**
     * 生成 FAQPage Schema（已在组件中实现）
     */
    public static function faqPage(array $faqs): array
    {
        $mainEntity = [];

        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq->questions,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answers),
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }
}