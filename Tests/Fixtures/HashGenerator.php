<?php
namespace Publero\FileBundle\Tests\Fixtures;

/**
 * @author Tomáš Pecsérke <tomas.pecsérke@publero.com>
 */
class HashGenerator
{
    public function generate()
    {
        return md5(rand());
    }
}
