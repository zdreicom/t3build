<?php
declare(strict_types=1);

namespace Z3\T3build\Service\Path;

use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Config;

class BasePaths
{

    /**
     * @var string
     */
    protected static $workingDirectory = '';

    /**
     * @var string
     */
    protected static $vendorDirectory = '';

    /**
     * @var string
     */
    protected static $binDirectory = '';

    /**
     * @var string
     */
    protected static $packagesDirectory = '';

    /**
     * @var string
     */
    protected static $t3buildVendorDirectory = '';

    /**
     * @var string
     */
    protected static $t3buildProjectRootDirectory = '';

    /**
     * @var string
     */
    protected static $t3buildDirectory = '';

    /**
     * @var string
     */
    protected static $t3buildTempDirectory = '';

    /**
     * @param string $workingDirectory
     * @param $composerConfig
     * @param string $type
     */
    public static function init(string $t3buildProjectRootDirectory, $composerConfig, string $type)
    {
        self::$t3buildProjectRootDirectory = $t3buildProjectRootDirectory;

        self::initWorkingDirectory($t3buildProjectRootDirectory, $composerConfig);

        self::$t3buildDirectory = self::$vendorDirectory . '/z3/t3build';
        if ($type === 'self') {
            self::$t3buildDirectory = self::$t3buildProjectRootDirectory;
        }
        self::$t3buildTempDirectory = self::$t3buildProjectRootDirectory . '/temporary';
        self::$t3buildVendorDirectory = self::$vendorDirectory;
    }

    /**
     * @param string $workingDirectory
     * @param $composerConfig
     */
    public static function initWorkingDirectory(string $workingDirectory, $composerConfig)
    {
        self::$workingDirectory = $workingDirectory;
        self::$vendorDirectory = self::$workingDirectory . '/vendor';
        self::$binDirectory = self::$workingDirectory . '/vendor/bin';

        if (property_exists($composerConfig, 'config')) {
            $config = $composerConfig->config;
            if (property_exists($config, 'vendor-dir')) {
                self::$vendorDirectory = self::$workingDirectory . '/' . $config->{'vendor-dir'};
            }
            if (property_exists($config, 'bin-dir')) {
                self::$binDirectory = self::$workingDirectory . '/' . $config->{'bin-dir'};
            }
        }
        self::addTypo3Path();
    }

    private static function addTypo3Path()
    {
        putenv('TYPO3_PATH_COMPOSER_ROOT=' . self::$workingDirectory);
        putenv('TYPO3_PATH_ROOT=' . self::$workingDirectory . '/web');
        putenv('TYPO3_PATH_WEB=' . self::$workingDirectory . '/web');
    }

    /**
     *
     */
    public static function postInitPackagesDirectory()
    {
        $packagesDirectory = self::$workingDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/packages/path', 'packages');
        if (is_dir($packagesDirectory)) {
            self::$packagesDirectory = $packagesDirectory;
        }
        if (Bootstrap::getRootPackage()->getType() === 'self') {
            self::$packagesDirectory = self::$workingDirectory . '/tests/Fixtures/Dummys';
        }
    }

    /**
     *
     */
    public static function postInitTempDirectory()
    {
        self::$t3buildTempDirectory = self::$t3buildProjectRootDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/temporary/path');
    }

    /**
     * @return string
     */
    public static function getT3buildDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$t3buildDirectory;
    }

    /**
     * @return string
     */
    public static function getBinDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$binDirectory;
    }

    /**
     * @return string
     */
    public static function getVendorDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$vendorDirectory;
    }

    /**
     * @return string
     */
    public static function getWorkingDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$workingDirectory;
    }

    /**
     * @return bool
     */
    public static function hasPackages(): bool
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        if (self::$packagesDirectory !== '') {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getPackagesDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$packagesDirectory;
    }

    /**
     * @return string
     */
    public static function getT3BuildProjectRootDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$t3buildProjectRootDirectory;
    }

    /**
     * @return string
     */
    public static function getT3buildVendorDirectory(): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        return self::$t3buildVendorDirectory;
    }

    /**
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public static function getT3buildTempDirectory(string $type = ''): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        $temp = self::$t3buildTempDirectory;
        if (!@mkdir($temp, 0777, true) && !is_dir($temp)) {
            throw new \Exception('Can not create temp directory in: ' . $temp);
        }
        if (strlen($type) > 0) {
            $temp .= '/' . $type;
            if (!@mkdir($temp, 0777, true) && !is_dir($temp)) {
                throw new \Exception('Can not create temp directory in: ' . $temp);
            }
        }
        return $temp;
    }

    /**
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public static function getT3buildArtifactDirectory(string $type = ''): string
    {
        echo 'WARNING: BasePaths is deprecated please use Config::getPaths() instead.' . "\n";
        $artifacts = self::$t3buildProjectRootDirectory . '/artifacts';
        if (!@mkdir($artifacts, 0777, true) && !is_dir($artifacts)) {
            throw new \Exception('Can not create artifact directory in: ' . $artifacts);
        }
        if (strlen($type) > 0) {
            $artifacts .= '/' . $type;
            if (!@mkdir($artifacts, 0777, true) && !is_dir($artifacts)) {
                throw new \Exception('Can not create directory in: ' . $artifacts);
            }
        }
        return $artifacts;
    }
}
