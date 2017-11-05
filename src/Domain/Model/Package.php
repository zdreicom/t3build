<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

class Package
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $type = 'composer-package';

    /**
     * @var string
     */
    protected $rootDirectory = '';

    /**
     * @var \Z3\T3build\Domain\Model\PackagePath[]
     */
    protected $paths = [];

    /**
     * Package constructor.
     * @param string $rootDirectory
     */
    public function __construct(string $rootDirectory, $composerConfig)
    {
        $this->rootDirectory = $rootDirectory;

        if (property_exists($composerConfig, 'name')) {
            $this->name = $composerConfig->name;
        }

        if (property_exists($composerConfig, 'type')) {
            $this->type = $composerConfig->type;
        }

        if (property_exists($composerConfig, 'type') && $composerConfig->type === 'typo3-cms-extension') {
            $this->type = 'typo3-cms-extension';
        }

        if (property_exists($composerConfig, 'type') && $composerConfig->type === 'typo3-cms') {
            $this->type = 'typo3-cms';
        }

        if (property_exists($composerConfig, 'type') && $composerConfig->type === 'neos-flow') {
            $this->type = 'neos-flow';
        }

        if (property_exists($composerConfig, 'type') && $composerConfig->type === 't3build-package') {
            $this->type = 't3build-package';
        }

        if (property_exists($composerConfig, 'type') && $composerConfig->type === 't3build-configuration') {
            $this->type = 't3build-configuration';
        }

        if ($this->name === 'z3/t3build') {
            $this->type = 'self';
        }

        $this->loadPaths();
    }

    public function loadPaths()
    {
        $this->paths['default'] = new PackagePath($this, 'default');
        $this->paths['sass'] = new PackagePath($this, 'sass');
        $this->paths['php'] = new PackagePath($this, 'php');
        $this->paths['typescript'] = new PackagePath($this, 'typescript');
        $this->paths['composer'] = new PackagePath($this, 'composer');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    /**
     * @param string $type
     * @return PackagePath
     */
    public function getPath(string $type): \Z3\T3build\Domain\Model\PackagePath
    {
        if (array_key_exists($type, $this->paths)) {
            return $this->paths[$type];
        }
        return $this->paths['default'];
    }
}
