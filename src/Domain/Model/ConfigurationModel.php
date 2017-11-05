<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Service\Config;

class ConfigurationModel
{
    /**
     * @var string
     */
    protected $configurationString = '';

    /**
     * @var string
     */
    protected $configurationFile = '';

    /**
     * @var array
     */
    protected $configurationArray = [];

    /**
     * Configuration constructor.
     * @param array $configurationArray
     * @param string $configurationString
     * @param string $configurationFile
     */
    public function __construct(array $configurationArray, string $configurationString, string $configurationFile)
    {
        $this->configurationArray = $configurationArray;
        $this->configurationString = $configurationString;
        $this->configurationFile = $configurationFile;
    }

    /**
     * @param string $key
     * @param string $format
     * @param array $configurationArray
     * @param string $custom
     * @return ConfigurationModel
     */
    public static function getConfigurationFromArrayOrCache(string $key, string $format, array $configurationArray, string $custom = ''): ConfigurationModel
    {
        $configurationFile = Config::getPaths()->getT3BuildTemporaryDirectory('configuration') . '/' . $key . '.' . $format;
        if (is_file($configurationFile)) {
            return self::getFromCache($key, $format);
        }
        return self::getConfigurationFromArray($key, $format, $configurationArray, $custom);
    }

    /**
     * @param string $key
     * @param string $format
     * @param array $configurationArray
     * @param string $custom
     * @return ConfigurationModel
     */
    public static function getConfigurationFromArray(string $key, string $format, array $configurationArray, string $custom = ''): ConfigurationModel
    {
        $configurationArray = $configurationArray;
        $configurationFile = Config::getPaths()->getT3BuildTemporaryDirectory('configuration') . '/' . $key . '.' . $format;
        $configurationFileArray = Config::getPaths()->getT3BuildTemporaryDirectory('configuration') . '/' . $key . '_array.yaml';

        switch ($format) {
            case 'json':
                $configurationString = \json_encode($configurationArray);
                break;
            case 'yaml':
                $configurationString = Yaml::dump($configurationArray);
                break;
            default:
                if (strlen($custom) > 0) {
                    $configurationString = $custom;
                } else {
                    throw new \InvalidArgumentException('The format must be yaml or json or add custom string');
                }
        }
        file_put_contents($configurationFile, $configurationString);
        file_put_contents($configurationFileArray, Yaml::dump($configurationArray));
        return new self($configurationArray, $configurationString, $configurationFile);
    }

    public static function getFromCache(string $key, string $format)
    {
        $configurationFile = Config::getPaths()->getT3BuildTemporaryDirectory('configuration') . '/' . $key . '.' . $format;
        if (!is_file($configurationFile)) {
            return false;
        }

        $configurationFileArray = Config::getPaths()->getT3BuildTemporaryDirectory('configuration') . '/' . $key . '_array.yaml';
        $configurationString = file_get_contents($configurationFileArray);
        $configurationArray = Yaml::parse($configurationString);

        switch ($format) {
            case 'json':
                $configurationString = \json_encode($configurationArray);
                break;
        }

        return new self($configurationArray, $configurationString, $configurationFile);
    }

    /**
     * @return string
     */
    public function getConfigurationString(): string
    {
        return $this->configurationString;
    }

    /**
     * @return string
     */
    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    /**
     * @return array
     */
    public function getConfigurationArray(): array
    {
        return $this->configurationArray;
    }
}
