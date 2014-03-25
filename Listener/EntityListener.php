<?php
namespace Publero\FileBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Publero\FileBundle\Entity\File;
use Publero\FrameworkBundle\Traits\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class EntityListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof File) {
            if ($entity->getHash() == null) {
                $generator = $this->container->get('publero_file.filename_generator');
                $entity->setHash($generator->generate());
            }

            if ($entity->getName() == null) {
                $entity->setName($entity->getHash());
            }

            if ($entity->getFile() !== null) {
                $this->handleUpload($entity);
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof File) {
            if ($args->hasChangedField('hash') && $args->getOldValue('hash') != null) {
                $filesystem = $this->container->get('publero_file.filesystem');
                $filesystem->rename($args->getOldValue('hash'), $args->getNewValue('hash'));
            }

            if ($entity->getFile() !== null) {
                $this->handleUpload($entity);
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof File) {
            $filesystem = $this->container->get('publero_file.filesystem');
            $filesystem->remove($entity->getHash());
        }
    }

    private function handleUpload(File $file)
    {
        $filesystem = $this->container->get('publero_file.filesystem');

        if ($file->getMimeType() == null) {
            $file->setMimeType($file->getFile()->getMimeType() ?: $file->getFile()->getClientMimeType());
        }
        if ($file->getOriginalName() == null) {
            $file->setOriginalName($file->getFile()->getClientOriginalName());
        }

        $file->setUploaded(new \DateTime());

        $file->getFile()->move($filesystem->getRoot(), $file->getHash());
        $file->setFile(null);

        $file->setFileSize(filesize($filesystem->makePathAbsolute($file->getHash())));
    }
}
