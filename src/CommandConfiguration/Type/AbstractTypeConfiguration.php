<?php
declare(strict_types=1);

namespace Z3\T3build\CommandConfiguration\Type;

use Z3\T3build\CommandConfiguration\ConfigurationInterface;
use Z3\T3build\Domain\Model\ConfigurationModel;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;

use Z3\T3build\Service\Config;

abstract class AbstractTypeConfiguration implements ConfigurationInterface
{
    /**
     * @var \Z3\T3build\Domain\Model\Package $package
     */
    protected $package = null;

    /**
     * @var \Z3\T3build\Domain\Model\Path $path
     */
    protected $path;

    /**
     * @var string
     */
    protected $task = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $fileSuffix = 'yaml';

    /**
     * @var string
     */
    protected $config = '';

    /**
     * @var \Z3\T3build\Domain\Model\ConfigurationModel
     */
    protected $configuration;

    /**
     * @var string
     */
    private $cashHash = '';

    /**
     * @var string
     */
    private $cashFile = '';

    /**
     * AbstractTypeConfiguration constructor.
     * @param Package $package
     * @param Path $path
     * @param string $task
     * @param string $type
     */
    public function __construct(Package $package, Path $path, string $task, string $type)
    {
        $this->package = $package;
        $this->path = $path;
        $this->task = $task;
        $this->type = $type;
        $this->cashHash = md5($package->getName() . $path->getHash() . $task . $type);
        $this->configure();
        $this->initConfig();
    }

    protected function configure()
    {
    }

    protected function initConfig()
    {
        $key = $this->task . '_' . $this->type . '_' . $this->cashHash;
        $this->makeConfig($key);
        $this->path->setConfigFile($this->configuration->getConfigurationFile());
    }

    /**
     * @param string $key
     */
    private function makeConfig(string $key)
    {
        Config::getProjectConfiguration()->setTage($this->package->getName());
        $currentConfigArray = Config::getProjectConfiguration()->getConfigurationArray($this->task . '/' . $this->type, []);
        $configArray  = $this->loadConfigArray($currentConfigArray, $this->package, $this->path, $this->task, $this->type);
        $this->configuration = ConfigurationModel::getConfigurationFromArrayOrCache($key, $this->fileSuffix, $configArray, $this->config);
        Config::getProjectConfiguration()->resetTags();
    }

    /**
     * @return string
     */
    public function getConfigurationString() : string
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getConfigurationFile() : string
    {
        return $this->path->getConfigFile();
    }

    /**
     * @return ConfigurationModel
     */
    public function getConfiguration(): ConfigurationModel
    {
        return $this->configuration;
    }

    /**
     * @param array $configArray
     * @param Package $package
     * @param Path $path
     * @param string $task
     * @param string $type
     * @return array
     */
    protected function loadConfigArray(array $configArray, Package $package, Path $path, string $task, string $type): array
    {
        return [];
    }
}
