<?php
namespace Publero\FileBundle\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

class RelativeFilesystem extends Filesystem
{
    /**
     * @var string
     */
    private $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->setRoot($root);
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param string $root
     * @return RelativeFilesystem
     * @throws \InvalidArgumentException
     */
    public function setRoot($root)
    {
        if (!is_dir($root)) {
            throw new \InvalidArgumentException("'$root' is not an existing directory");
        }
        $this->root = realpath($root);

        return $this;
    }

    /**
     * @param string|array|\Traversable $path
     * @return string
     */
    public function makePathAbsolute($path)
    {
        if (is_array($path) || $path instanceof \Traversable) {
            return array_map(function($file) {
                return $this->makePathAbsolute($file);
            }, (array) $path);
        }

        return $this->isAbsolutePath($path) ? $path : $this->getRoot() . '/' . $path;
    }

    public function makePathRelative($endPath, $startPath = null)
    {
        return rtrim(parent::makePathRelative($endPath, $startPath === null ? $this->getRoot() : $startPath), '/');
    }

    public function copy($originFile, $targetFile, $override = false)
    {
        parent::copy($this->makePathAbsolute($originFile), $this->makePathAbsolute($targetFile), $override);
    }

    public function mkdir($dirs, $mode = 0777)
    {
        parent::mkdir($this->makePathAbsolute($dirs), $mode);
    }

    public function exists($files)
    {
        return parent::exists($this->makePathAbsolute($files));
    }

    public function touch($files, $time = null, $atime = null)
    {
        parent::touch($this->makePathAbsolute($files), $time, $atime);
    }

    public function remove($files)
    {
        parent::remove($this->makePathAbsolute($files));
    }

    public function chmod($files, $mode, $umask = 0000, $recursive = false)
    {
        parent::chmod($this->makePathAbsolute($files), $mode, $umask, $recursive);
    }

    public function chown($files, $user, $recursive = false)
    {
        parent::chown($this->makePathAbsolute($files), $user, $recursive);
    }

    public function chgrp($files, $group, $recursive = false)
    {
        parent::chgrp($this->makePathAbsolute($files), $group, $recursive);
    }

    public function rename($origin, $target)
    {
        parent::rename($this->makePathAbsolute($origin), $this->makePathAbsolute($target));
    }

    public function symlink($originDir, $targetDir, $copyOnWindows = false)
    {
        parent::symlink($this->makePathAbsolute($originDir), $this->makePathAbsolute($targetDir), $copyOnWindows);
    }

    public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = array())
    {
        parent::mirror($this->makePathAbsolute($originDir), $this->makePathAbsolute($targetDir), $iterator, $options);
    }
}
