<?php
namespace Publero\FileBundle\DependencyInjection;

use Publero\FileBundle\DependencyInjection\Compiler\CheckHashGeneratorPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PubleroFileExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (empty($config['hash_generator'])) {
            throw new InvalidConfigurationException('"hash_generator" must be defined in configuration');
        }

        $container->setAlias('publero_file.hash_generator', $config['hash_generator']);
        $container->addCompilerPass(new CheckHashGeneratorPass(), PassConfig::TYPE_AFTER_REMOVING);

        $container->setParameter('publero_file.directory', realpath($config['directory']));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
