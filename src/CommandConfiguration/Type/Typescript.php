<?php
declare(strict_types=1);

namespace Z3\T3build\CommandConfiguration\Type;

use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;

class Typescript extends AbstractTypeConfiguration
{
    protected function configure()
    {
        $this->fileSuffix = 'json';
    }

    /**
     * @var array
     */
    private $configArray = [];

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
        $this->configArray = $configArray;
        if ($task === 'build') {
            $this->configArray['include'] = [
                $path->getInputPath() . '/**/*.ts'
            ];
            $this->configArray['compilerOptions']['outFile'] = $path->getOutputPath() . '/main.js';
        }
        return $this->configArray;
    }

    /**
     * @param array $configArray
     * @param Package $package
     * @param Path $path
     * @param string $task
     * @param string $type
     * @return string
     */
    protected function loadConfig(array $configArray, Package $package, Path $path, string $task, string $type) : string
    {
        return json_encode($this->configArray);
    }
}
