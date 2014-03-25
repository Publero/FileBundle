<?php
namespace Publero\FileBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Tomáš Pecsérke <tomas.pecserke@publero.com>
 */
class CheckHashGeneratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $alias = $container->getAlias('publero_file.hash_generator');
        $definition = $container->getDefinition($alias->__toString());
        if (!method_exists($definition->getClass(), 'generate')) {
            throw new InvalidArgumentException('publero_file.hash_generator service class has to contain method "generate"');
        }
    }
}
