<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

use Z3\T3build\Service\Config;

class PackagePath
{
    /**
     * @var string[]
     */
    protected $input = [];

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @var \Z3\T3build\Domain\Model\Package $package
     */
    private $package = '';

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var bool
     */
    private $isConfigInit = false;

    /**
     * Path constructor.
     * @param \Z3\T3build\Domain\Model\Package $package
     * @param string $type
     */
    public function __construct(Package $package, string $type)
    {
        $this->package = $package;
        $this->type = $type;
    }

    private function initConfig()
    {
        Config::getProjectConfiguration()->setTage($this->package->getType());
        foreach (Config::getProjectConfiguration()->getConfigurationArrayOfString('workingDirectories/' . $this->type . '/path', []) as $inputPath) {
            $path = $this->package->getRootDirectory() . '/' . $inputPath;
            $this->input[] = $path;
        }
        $this->output = $this->package->getRootDirectory() . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/' . $this->type . '/outputPath', '');
        Config::getProjectConfiguration()->resetTags();
    }

    /**
     * @return string[]
     */
    public function getInput(): array
    {
        if (!$this->isConfigInit) {
            $this->initConfig();
        }
        return $this->input;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        if (!$this->isConfigInit) {
            $this->initConfig();
        }
        return $this->output;
    }
}
