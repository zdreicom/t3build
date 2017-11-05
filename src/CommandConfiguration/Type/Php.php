<?php
declare(strict_types=1);

namespace Z3\T3build\CommandConfiguration\Type;

use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;

class Php extends AbstractTypeConfiguration
{
    protected function configure()
    {
        $this->fileSuffix = 'php';
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
        $this->config = $this->getDummy($path->getInputPath(), var_export($configArray, true));
        return $configArray;
    }

    /**
     * @param string $inputPath
     * @return string
     */
    private function getDummy($inputPath, $config) : string
    {
        $dummy = '
            <?php
                if (PHP_SAPI !== \'cli\') {
                    die(\'This script supports command line usage only. Please check your command.\');
                }
                
                $finder = PhpCsFixer\Finder::create();
                ' . '$finder->in("' . $inputPath . '");' . '
                
                return PhpCsFixer\Config::create()
                    ->setRiskyAllowed(true)
                    ->setRules(' . $config . ')
                    ->setFinder($finder);
        ';

        return $dummy;
    }
}
