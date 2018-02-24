<?php

namespace Jeeves;

use Symfony\Component\Filesystem\Filesystem;

class Jeeves
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $buildDirectory;

    /**
     * Jeeves constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return bool
     */
    public function jenkinsFileExists()
    {
        return $this->filesystem->exists(__DIR__ . '/../Jenkinsfile');
    }

    /**
     * @return string
     */
    public function getBuildDirectory(): string
    {
        return $this->buildDirectory;
    }

    /**
     * @param string $buildDirectory
     */
    public function setBuildDirectory(string $buildDirectory)
    {
        $this->buildDirectory = $buildDirectory;
    }
}
