<?php
declare(strict_types=1);

namespace Z3\T3build\Service\Configuration;

use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Utility\ArrayUtility;

class Configuration
{
    private $generalConfig = [];
    private $currentConfig = [];
    private $configArrays = [];

    /**
     * @param array $configurationDirectorys
     */
    public function __construct(array $configuration = [])
    {
        if ($this->generalConfig) {
            $this->generalConfig = false;
        }
        $this->addConfiguration($configuration);
    }

    /**
     * @param array $configuration
     */
    public function addConfiguration(array $configuration)
    {
        if ($this->generalConfig === false) {
            $this->generalConfig = [];
        }
        $this->configArrays[] = $configuration;

        // make globel array over all config files
        foreach ($this->configArrays as $configArray) {
            $this->generalConfig = ArrayUtility::arrayMergeRecursive($this->generalConfig, $configArray);
        }
        $this->currentConfig = $this->generalConfig;
    }

    /**
     * @param string $configuration
     */
    public function addConfigurationYaml(string $configuration)
    {
        $this->addConfiguration(Yaml::parse($configuration));
    }

    /**
     * @param string[] $tags
     */
    public function setTages(array $tags)
    {
        foreach ($tags as $tag) {
            $this->setTage($tag);
        }
    }

    /**
     * @param string $tag
     */
    public function setTage(string $tag)
    {
        foreach ($this->configArrays as $configArray) {
            $this->currentConfig = ArrayUtility::arrayMergeRecursiveCondition($this->currentConfig, $configArray, $tag);
        }
    }

    /**
     */
    public function resetTags()
    {
        $this->currentConfig = $this->generalConfig;
    }

    /**
     * @param string $path
     * @param string $default
     * @return string
     */
    public function getConfigurationString(string $path, string $default = ''): string
    {
        $configuration = $this->getConfiguration($path);
        if (!is_string($configuration)) {
            return $default;
        }

        return $configuration;
    }

    /**
     * @param string $path
     * @param int $default
     * @return int
     */
    public function getConfigurationInt(string $path, int $default = 0): int
    {
        $configuration = $this->getConfiguration($path);
        if (!is_numeric($configuration)) {
            return $default;
        }
        $out = (int) $configuration;
        if ($out != $configuration) {
            return $default;
        }
        return $out;
    }

    /**
     * @param string $path
     * @param array $default
     * @return array
     */
    public function getConfigurationArrayOfString(string $path, array $default = []): array
    {
        $configuration = $this->getConfiguration($path);
        if (!is_array($configuration)) {
            return $default;
        }

        foreach ($configuration as $item) {
            if (!is_string($item)) {
                return $default;
            }
        }

        return $configuration;
    }

    /**
     * @param string $path
     * @param array $default
     * @return array
     */
    public function getConfigurationArray(string $path, array $default = []): array
    {
        $configuration = $this->getConfiguration($path);
        if (!is_array($configuration)) {
            return $default;
        }

        return $configuration;
    }

    /**
     * @param string $path
     * @return array|bool|mixed
     */
    private function getConfiguration(string $path)
    {
        $pathArray = explode('/', $path);
        $currentConfigPosition = $this->currentConfig;

        foreach ($pathArray as $pathSegment) {
            if (!is_array($currentConfigPosition) || !array_key_exists($pathSegment, $currentConfigPosition)) {
                return false;
            }
            $currentConfigPosition = $currentConfigPosition[$pathSegment];
        }

        return $currentConfigPosition;
    }

    /**
     * @param string $path
     * @param string $value
     * @return Configuration
     */
    public function setConfigurationString(string $path, string $value): Configuration
    {
        $this->setConfiguration($path, $value);
        return $this;
    }

    /**
     * @param string $path
     * @param int $value
     * @return Configuration
     */
    public function setConfigurationInt(string $path, int $value): Configuration
    {
        $this->setConfiguration($path, $value);
        return $this;
    }

    /**
     * @param string $path
     * @param array $value
     * @return Configuration
     */
    public function setConfigurationArrayOfString(string $path, array $value): Configuration
    {
        foreach ($value as $item) {
            if (!is_string($item)) {
                return $this;
            }
        }
        $this->setConfiguration($path, $value);
        return $this;
    }

    /**
     * @param string $path
     * @param $value
     */
    private function setConfiguration(string $path, $value)
    {
        $pathArray = explode('/', $path);
        $currentConfigPosition = &$this->generalConfig;

        for ($i = 0; $i < count($pathArray); $i++) {
            $pathSegment = $pathArray[$i];
            if ($i < count($pathArray) - 1) {
                if (!array_key_exists($pathSegment, $currentConfigPosition)) {
                    $currentConfigPosition[$pathSegment] = [];
                }
                $currentConfigPosition = &$currentConfigPosition[$pathSegment];
            } else {
                $currentConfigPosition[$pathSegment] = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function exportConfigurationYaml(): string
    {
        return Yaml::dump($this->generalConfig, 5, 2);
    }
}
