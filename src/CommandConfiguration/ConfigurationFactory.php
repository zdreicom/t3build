<?php
declare(strict_types=1);

namespace Z3\T3build\CommandConfiguration;

use Z3\T3build\CommandConfiguration\Type\Php;
use Z3\T3build\CommandConfiguration\Type\Sass;
use Z3\T3build\CommandConfiguration\Type\Typescript;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;

class ConfigurationFactory
{

    /**
     * @param Package $package
     * @param Path $path
     * @param string $task
     * @param string $type
     * @return ConfigurationInterface
     */
    public static function getCommandConfiguration(Package $package, Path $path, string $task, string $type) : ConfigurationInterface
    {
        switch ($type) {
            case 'sass':
                $config = new Sass($package, $path, $task, $type);
                return $config;
                break;
            case 'php':
                $config = new Php($package, $path, $task, $type);
                return $config;
                break;
            case 'typescript':
                $config = new Typescript($package, $path, $task, $type);
                return $config;
                break;
        }
    }
}
