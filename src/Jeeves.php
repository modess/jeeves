<?php

namespace Jeeves;

use Symfony\Component\Filesystem\Filesystem;

class Jeeves
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function jenkinsFileExists()
    {
        return $this->filesystem->exists(__DIR__ . '/../Jenkinsfile');
    }
}
