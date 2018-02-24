<?php

namespace Jeeves;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class JeevesTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Jeeves
     */
    private $jeeves;

    public function setUp()
    {
        $this->filesystem = \Mockery::mock(Filesystem::class);
        $this->jeeves = new Jeeves($this->filesystem);
    }
}
