<?php

namespace AmzsCMS\ArticleBundle\Twig\Extension;

use AmzsCMS\ArticleBundle\Utils\AssetUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArticleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_path_article_cms_asset', [AssetUtil::class, 'getPrefixBundle']),
        ];
    }

}
