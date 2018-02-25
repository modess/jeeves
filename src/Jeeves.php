<?php

namespace Jeeves;

use League\Flysystem\Filesystem;

class Jeeves
{
    const COMPOSER_PATH = 'PATH=vendor/bin:`composer config --global home`/vendor/bin:$PATH';

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $srcDirectory;

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
     * @param Filesystem $fileSystem
     * @param string     $srcDirectory
     */
    public function __construct(Filesystem $fileSystem, string $srcDirectory)
    {
        $this->fileSystem = $fileSystem;
        $this->srcDirectory = $srcDirectory;
    }

    /**
     * @return bool
     */
    public function jenkinsFileExists(): bool
    {
        return $this->fileSystem->has('Jenkinsfile');
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
        $stub = $this->fileSystem->read($this->srcDirectory . '/stubs/Jenkinsfile');

        if (empty($this->getSlackChannel())) {
            $stub = preg_replace('/(\{\{slack\}\}(.|\n)*\{\{\/slack\}\})/mU', '', $stub);
        } else {
            $stub = str_replace('{{slack\}}', '', $stub);
            $stub = str_replace('{{/slack}}', '', $stub);
            $stub = str_replace('{{slackChannel}}', $this->getSlackChannel(), $stub);
        }

        $stub = str_replace('{{buildDirectory}}', $this->getBuildDirectory(), $stub);
        $stub = str_replace('{{sourceDirectory}}', $this->getSourceDirectory(), $stub);
        $stub = str_replace('{{composerPath}}', static::COMPOSER_PATH, $stub);

        $this->fileSystem->put('Jenkinsfile', $stub);
    }

    /**
     * @return bool
     */
    public function phpcsXmlExists(): bool
    {
        return $this->fileSystem->has('phpcs.xml');
    }

    /**
     * Copy phpcs.xml
     */
    public function copyPhpcsXml()
    {
        $this->fileSystem->copy(
            $this->srcDirectory . '/stubs/phpcs.xml',
            'phpcs.xml'
        );
    }

    /**
     * @return bool
     */
    public function phpmdXmlExists(): bool
    {
        return $this->fileSystem->has('phpmd.xml');
    }

    /**
     * Copy phpmd.xml
     */
    public function copyPhpmdXml()
    {
        $this->fileSystem->copy(
            $this->srcDirectory . '/stubs/phpmd.xml',
            'phpmd.xml'
        );
    }
}
