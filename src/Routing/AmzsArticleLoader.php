<?php

namespace AmzsCMS\ArticleBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class AmzsArticleLoader extends Loader
{
    private $isLoaded = false;
    public function load($resource, ?string $type = null): RouteCollection
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "amzs_article_route_loader" loader twice');
        }
        $type = 'yaml';
        $routes = new RouteCollection();

        /** @var $importedRoutes */
        $importedRoutes = $this->import($resource, $type);
        $routes->addCollection($importedRoutes);
        $routes->addPrefix('/cms');

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'amzs_article_route_loader' == $type;
    }
}