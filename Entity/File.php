<?php
namespace Publero\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="publero_file_file")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="file_type", type="string", length=16)
 *
 * @author Tomáš Pecsérke <tomas.pecserke@publero.com>
 */
class File
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
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", nullable=true)
     */
    private $originalName;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", nullable=true)
     */
    private $mimeType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer")
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", unique=true)
     */
    private $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploaded", type="datetime", nullable=true)
     */
    private $uploaded;

    /**
     * @var UploadedFile
     *
     * @Assert\File
     */
    private $file;

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
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     * @return File
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param integer $fileSize
     * @return File
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return File
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUploaded()
    {
        return $this->uploaded;
    }

    /**
     * @param \DateTime $file
     * @return File
     */
    public function setUploaded(\DateTime $uploaded = null)
    {
        $this->uploaded = $uploaded;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     * @return File
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        if ($file !== null) {
            $this->uploaded = new \DateTime();
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getHash();
    }
}
