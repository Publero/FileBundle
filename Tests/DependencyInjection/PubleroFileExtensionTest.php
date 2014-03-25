<?php
namespace Publero\FileBundle\Tests\DependencyInjection;

use Publero\Component\Test\ExtensionTestCase;
use Symfony\Component\DependencyInjection\Definition;
use Publero\FileBundle\DependencyInjection\PubleroFileExtension;

class PubleroFileExtensionTest extends ExtensionTestCase
{
    /**
     * @var string
     */
    private $hashGeneratorName = 'hash_generator_service_name';

    /**
     * @return string
     */
    protected function getDirectory()
    {
        return realpath(sys_get_temp_dir());
    }

    public function getConfig()
    {
        return <<<EOF
directory: {$this->getDirectory()}
hash_generator: {$this->hashGeneratorName}
EOF;
    }

    public function setUp()
    {
        parent::setUp();

        $container = $this->getContainer();
        $config = $this->getParsedConfig();

        $generatorDefinition = new Definition();
        $generatorDefinition->setClass('Publero\FileBundle\Tests\Fixtures\HashGenerator');
        $container->setDefinition($config['hash_generator'], $generatorDefinition);
    }

    public function testLoad()
    {
        $container = $this->getContainer();
        $config = $this->getParsedConfig();

        $this->loadExtension(new PubleroFileExtension(), array($config));
        $container->compile();

        $generatorDefinition = $container->findDefinition('publero_file.hash_generator');

        $this->assertNotNull($generatorDefinition);
        $this->assertTrue(method_exists($generatorDefinition->getClass(), 'generate'));

        $this->assertEquals($this->getDirectory(), $container->getParameter('publero_file.directory'));
    }

    public function testLoadDirectoryDoesntExist()
    {
        $config = $this->getParsedConfig();
        $config['directory'] = $this->getDirectory() . '/' . uniqid();
        $this->loadExtension(new PubleroFileExtension(), array($config));
        rmdir($config['directory']);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionIfDirectoryIsNotSetInConfiruation()
    {
        $config = $this->getParsedConfig();
        unset($config['directory']);
        $this->loadExtension(new PubleroFileExtension(), array($config));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionIfDirectoryIsEmptytInConfiruation()
    {
        $config = $this->getParsedConfig();
        $config['directory'] = null;
        $this->loadExtension(new PubleroFileExtension(), array($config));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionIfHashGeneratorNameIsEmpty()
    {
        $config = $this->getParsedConfig();
        $config['hash_generator'] = '';
        $this->loadExtension(new PubleroFileExtension(), array($config));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionIfHashGeneratorNameIsNull()
    {
        $config = $this->getParsedConfig();
        $config['hash_generator'] = null;
        $this->loadExtension(new PubleroFileExtension(), array($config));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionIfHashGeneratorNameIsNotSetInConfiguration()
    {
        $config = $this->getParsedConfig();
        unset($config['hash_generator']);
        $this->loadExtension(new PubleroFileExtension(), array($config));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testLoadThrowsExceptionIfHashGeneratorDoesNotExist()
    {
        $config = $this->getParsedConfig();
        $config['hash_generator'] = 'i_do_not_exist';
        $this->loadExtension(new PubleroFileExtension(), array($config));
        $this->getContainer()->compile();
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testLoadThrowsExceptionIfHashGeneratorDoesNotContainGenerateMethod()
    {
        $definition = new Definition();
        $definition->setClass('\PHPUnit_Framework_TestCase');

        $container = $this->getContainer();
        $container->setDefinition('service', $definition);

        $config = $this->getParsedConfig();
        $config['hash_generator'] = 'service';
        $this->loadExtension(new PubleroFileExtension(), array($config));
        $this->getContainer()->compile();
    }
}
