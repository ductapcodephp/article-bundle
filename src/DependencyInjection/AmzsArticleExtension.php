<?php

namespace AmzsCMS\ArticleBundle\DependencyInjection;


use AmzsCMS\ArticleBundle\Constant\ArticleRoute;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AmzsArticleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $container->setParameter('amz.user_bundle.default_password', $config['default_password']);
    }
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', [
            'globals' => [
                'amzs_article_index_route' => ArticleRoute::ROUTE_INDEX,
                'amzs_article_data_route' => ArticleRoute::ROUTE_DATA,
                'amzs_article_add_route' => ArticleRoute::ROUTE_ADD,
                'amzs_article_edit_route' => ArticleRoute::ROUTE_EDIT,
                'amzs_article_delete_route' => ArticleRoute::ROUTE_DELETE,
            ],
        ]);
    }
}