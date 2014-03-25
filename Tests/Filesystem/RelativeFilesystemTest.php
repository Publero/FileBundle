<?php
namespace Publero\FileBundle\Tests\Filesystem;

use Publero\FileBundle\Filesystem\RelativeFilesystem;

class RelativeFilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RelativeFilesystem
     */
    private static $filesystem;

    /**
     * @var string
     */
    private static $root;

    /**
     * @var string[]
     */
    private static $filesToRemove;

    public static function setUpBeforeClass()
    {
        self::$root = sys_get_temp_dir() . '/' . uniqid('test_filesystem_');
        mkdir(self::$root);
        self::$filesystem = new RelativeFilesystem(self::$root);
    }

    protected function setUp()
    {
        self::$filesystem->setRoot(self::$root);
        self::$filesToRemove = array();
    }

    protected function tearDown()
    {
        foreach (self::$filesToRemove as $file) {
            if (is_dir($file)) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        self::$filesToRemove = null;
    }

    public static function tearDownAfterClass()
    {
        self::$filesystem = null;
        rmdir(self::$root);
        self::$root = null;
    }

    public function testGetAndSetRoot()
    {
        $this->assertEquals(self::$root, self::$filesystem->getRoot());
        $this->assertTrue(is_dir(self::$filesystem->getRoot()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAndSetRootDoesNotExist()
    {
        self::$filesystem->setRoot(self::$root . '/i_dont_exist');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAndSetRootIsNotADirectory()
    {
        $file = tempnam(self::$root, 'test_');
        self::$filesToRemove[] = $file;
        self::$filesystem->setRoot($file);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAndSetRootIsNull()
    {
        self::$filesystem->setRoot(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAndSetRootIsEmpty()
    {
        self::$filesystem->setRoot('');
    }

    public function testMakePathRelative()
    {
        $dir = self::$root . '/test';
        mkdir($dir);
        $file = tempnam($dir, 'test_');
        self::$filesToRemove[] = $file;
        self::$filesToRemove[] = $dir;

        $fileName = substr($file, strlen(self::$root) + 1);

        $this->assertEquals($fileName, self::$filesystem->makePathRelative($file));
    }
}
