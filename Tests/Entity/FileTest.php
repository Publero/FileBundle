<?php
namespace Publero\FileBundle\Entity;

use Publero\FileBundle\Filesystem\RelativeFilesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Publero\Component\Test\DatabaseTestCase;
use Publero\FileBundle\Entity\File;

/**
 * @author Tomáš Pecsérke <tomas.pecserke@publero.com>
 */
class FileTest extends DatabaseTestCase
{
    /**
     * @var RelativeFilesystem
     */
    private static $filesystem;

    /**
     * @var string
     */
    private $removeFile;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var File
     */
    private $file;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$filesystem = self::getContainer()->get('publero_file.filesystem');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->fileName = uniqid('test_');
        self::$filesystem->touch($this->fileName);
        $path = realpath(self::$filesystem->makePathAbsolute($this->fileName));
        $this->removeFile = $path;
        file_put_contents($path, sha1(rand()));
        $this->hash = sha1_file($path);
        $originalFileName = $this->fileName . '.test';

        $this->file = new File();
        $this->file->setFile(new UploadedFile($path, $originalFileName, null, null, null, true));
        self::$em->persist($this->file);
        self::$em->flush();

        $this->removeFile = realpath(self::$filesystem->makePathAbsolute($this->file->getName()));
    }

    public function tearDown()
    {
        if (isset($this->removeFile)) {
            unlink($this->removeFile);
            $this->removeFile = null;
        }

        parent::tearDown();
    }

    public function testPersist()
    {
        $path = $this->removeFile;

        $this->assertNotEmpty($this->file->getHash());
        $this->assertEquals($this->fileName . '.test', $this->file->getOriginalName());
        $this->assertNotEmpty($this->file->getMimeType());
        $this->assertEquals(filesize($path), $this->file->getFileSize());
        $this->assertFalse(self::$filesystem->exists($this->fileName));
        $this->assertEquals($this->hash, sha1_file($path));
    }

    public function testRename()
    {
        $oldFileName = $this->file->getHash();

        $this->file->setHash("$oldFileName.test");
        self::$em->persist($this->file);
        self::$em->flush();
        $this->removeFile = realpath(self::$filesystem->makePathAbsolute($this->file->getHash()));

        $this->assertFalse(self::$filesystem->exists($oldFileName));
        $this->assertEquals($this->hash, sha1_file($this->removeFile));
    }

    public function testReuploadAndRename()
    {
        $oldFileName = $this->file->getName();

        $fileName = uniqid('test_');
        self::$filesystem->touch($fileName);
        $this->removeFile = $fileName;
        $path = realpath(self::$filesystem->makePathAbsolute($fileName));
        file_put_contents($path, sha1(rand()));
        $hash = sha1_file($path);

        self::$em->refresh($this->file);
        $this->file->setFile(new UploadedFile($path, '', null, null, null, true));
        $this->file->setHash($this->file->getHash() . '.test');
        self::$em->persist($this->file);
        self::$em->flush();

        $path = self::$filesystem->makePathAbsolute($this->file->getHash());
        $this->removeFile = $path;

        $this->assertNotEmpty($this->file->getMimeType());
        $this->assertEquals(filesize($path), $this->file->getFileSize());
        $this->assertFalse(self::$filesystem->exists($fileName));
        $this->assertEquals($hash, sha1_file($path));
    }

    public function testRemove()
    {
        $oldFileName = $this->file->getName();

        self::$em->remove($this->file);
        self::$em->flush();
        $this->removeFile = null;

        $this->assertFalse(self::$filesystem->exists($oldFileName));
    }
}
