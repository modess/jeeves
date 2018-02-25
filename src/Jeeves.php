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
    private $rootDirectory;

    /**
     * @var string
     */
    private $buildDirectory;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $slackChannel;

    /**
     * Jeeves constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, string $rootDirectory)
    {
        $this->filesystem = $filesystem;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * @return bool
     */
    public function jenkinsFileExists(): bool
    {
        return $this->filesystem->exists($this->rootDirectory . '/Jenkinsfile');
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

    /**
     * @return string
     */
    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    /**
     * @param string $sourceDirectory
     */
    public function setSourceDirectory(string $sourceDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    /**
     * @return string
     */
    public function getSlackChannel(): string
    {
        return $this->slackChannel;
    }

    /**
     * @param string $slackChannel
     */
    public function setSlackChannel(string $slackChannel)
    {
        if (!empty($slackChannel) && !strstr($slackChannel, '#')) {
            $slackChannel = '#' . $slackChannel;
        }

        $this->slackChannel = $slackChannel;
    }

    /**
     * Generate Jenkinsfile
     */
    public function generateJenkinsFile()
    {
        $stub = file_get_contents(__DIR__ . '/stubs/Jenkinsfile');

        if (empty($this->getSlackChannel())) {
            $stub = preg_replace('/(\{\{slack\}\}(.|\n)*\{\{\/slack\}\})/mU', '', $stub);
        } else {
            $stub = str_replace('{{slack\}}', '', $stub);
            $stub = str_replace('{{/slack}}', '', $stub);
            $stub = str_replace('{{slackChannel}}', $this->getSlackChannel(), $stub);
        }

        $stub = str_replace('{{buildDirectory}}', $this->getBuildDirectory(), $stub);
        $stub = str_replace('{{sourceDirectory}}', $this->getSourceDirectory(), $stub);

        $this->filesystem->dumpFile($this->rootDirectory . '/Jenkinsfile', $stub);
    }
}
