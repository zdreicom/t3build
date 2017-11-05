<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Repository;

use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Config;
use Z3\T3build\Utility\FileDirectoryUtility;

class PackageRepository
{
    /**
     * @var Package[]
     */
    protected static $packages = [];

    /**
     * @return array
     */
    public static function getPackages() : array
    {
        if (count(self::$packages) === 0) {
            self::loadPackages();
        }
        return self::$packages;
    }

    private static function loadPackages()
    {
        self::$packages[] = Bootstrap::getRootPackage();

        $packagePaths = FileDirectoryUtility::getDirectoriesInPath(Config::getPaths()->getWorkingPackagesDirectory(), true);
        foreach ($packagePaths as $packagePath) {
            $composerJsonPath = $packagePath . '/composer.json';

            if (!is_file($composerJsonPath)) {
                continue;
            }

            $composerConfig = json_decode(file_get_contents($composerJsonPath));
            if ($composerConfig === null) {
                continue;
            }

            self::$packages[] = new Package($packagePath, $composerConfig);
        }
    }
}
