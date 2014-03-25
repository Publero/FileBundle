<?php
namespace Publero\FileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="publero_file_file_group")
 *
 * @author Tomáš Pecsérke <tomas.pecserke@publero.com>
 * @see https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md#basic-usage-examples
 */
class FileGroup
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @var FileGroup
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="FileGroup", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="FileGroup", mappedBy="parent")
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="File", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="publero_file_file_group_has_file",
     *     joinColumns={@ORM\JoinColumn(name="file_group_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return FileGroup
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param FileGroup $parent
     * @return FileGroup
     */
    public function setParent(FileGroup $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return FileGroup
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return string[]
     */
    private function getFileHashes()
    {
        return array_map(function (File $file) {
            return $file->getHash();
        }, $this->getFiles()->toArray());
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function hasFile($hash)
    {
        return in_array($hash, $this->getFileHashes());
    }

    /**
     * @param File $file
     * @return FileGroup
     */
    public function addFile(File $file)
    {
        if (!$this->hasFile($file->getHash())) {
            $this->files[] = $file;
        }

        return $this;
    }

    /**
     * @param File $file
     * @return bool
     */
    public function removeFile(File $file)
    {
        return $this->files->removeElement($files);
    }
}
