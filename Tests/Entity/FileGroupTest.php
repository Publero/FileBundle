<?php
namespace Publero\FileBundle\Entity;

use Publero\Component\Test\DatabaseTestCase;
use Publero\FileBundle\Entity\File;

/**
 * @author Tomáš Pecsérke <tomas.pecserke@publero.com>
 */
class FileGroupTest extends DatabaseTestCase
{
    public function testHasFile()
    {
        $file1 = new File();
        $file1->setFileSize(1)->setMimeType('text/plain')->setName('example.txt')->setOriginalName('example.txt');
        self::$em->persist($file1);
        $file2 = new File();
        $file2->setFileSize(1)->setMimeType('text/plain')->setName('example.txt')->setOriginalName('example.txt');
        self::$em->persist($file2);

        $group = new FileGroup();
        $group->setName('test_group');
        $group->getFiles()->add($file1);
        $group->getFiles()->add($file2);
        self::$em->persist($group);

        $this->assertCount(2, $group->getFiles());
        $this->assertTrue($group->hasFile($file1->getHash()));
        $this->assertTrue($group->hasFile($file2->getHash()));
        $this->assertFalse($group->hasFile('i_dont_exist'));
    }
}
