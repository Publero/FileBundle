<?php
namespace Publero\FileBundle\Tests\DependencyInjection\Compiler;

use Publero\FileBundle\DependencyInjection\Compiler\CheckHashGeneratorPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckHashGeneratorPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testProcessThrowsExceptionIfHashGeneratorDoesNotContainGenerateFunction()
    {
        $definition = new Definition();
        $definition->setClass('PHPUnit_Framework_TestCase');

        $container = new ContainerBuilder();
        $container->setDefinition('filesystem', $definition);
        $container->setAlias('publero_file.hash_generator', 'filesystem');

        $compilerPass = new CheckHashGeneratorPass();
        $compilerPass->process($container);
    }
}
