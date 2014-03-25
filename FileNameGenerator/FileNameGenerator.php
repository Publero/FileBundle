<?php
namespace Publero\FileBundle\FileNameGenerator;

use Publero\Component\CodeGenerator\CodeGeneratorInterface;
use Publero\FileBundle\Filesystem\RelativeFilesystem;

class FileNameGenerator implements CodeGeneratorInterface
{
    /**
     * @var RelativeFilesystem
     */
    private $filesystem;

    /**
     * @var CodeGeneratorInterface
     */
    private $nameGenerator;

    /**
     * @return RelativeFilesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param RelativeFilesystem $filesystem
     */
    public function setFilesystem(RelativeFilesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return CodeGeneratorInterface
     */
    public function getNameGenerator()
    {
        return $this->nameGenerator;
    }

    public function setNameGenerator(CodeGeneratorInterface $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function generate()
    {
        do {
            $fileName = $this->getNameGenerator()->generate();
        } while($this->getFilesystem()->exists($fileName));

        return $fileName;
    }
}
