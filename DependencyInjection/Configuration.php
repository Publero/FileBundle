<?php
namespace Publero\FileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('publero_file');

        $rootNode
            ->children()
                ->scalarNode('directory')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($dir) {
                            return !(is_dir($dir) || mkdir($dir, 0770, true));
                        })
                        ->thenInvalid('%s is not a valid directory and could not be created.')
                    ->end()
                ->end()
                ->scalarNode('hash_generator')->cannotBeEmpty()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
