<?php
declare(strict_types=1);

namespace Z3\T3build\Service;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Domain\Model\GitProject;
use Z3\T3build\Service\Configuration\ProjectConfiguration;
use Z3\T3build\Service\Configuration\UserConfiguration;
use Z3\T3build\Service\Path\Paths;
use Z3\T3build\Utility\FileDirectoryUtility;

class Config
{
    /**
     * @var ProjectConfiguration
     */
    private static $projectConfiguration;

    /**
     * @var UserConfiguration
     */
    private static $userConfiguration;

    /**
     * @var Paths
     */
    private static $paths;

    /**
     * @var GitProject
     */
    private static $project;

    /**
     * @param string $projectDirectory
     * @param $composerConfig
     * @param string $type
     */
    public static function preInitPaths(string $projectDirectory, $composerConfig, string $type)
    {
        self::$paths = new Paths();
        self::$paths->preInitPaths($projectDirectory, $composerConfig, $type);
    }

    /**
     * @param array $vendorPackages
     */
    public static function init(array $vendorPackages)
    {
        self::projectProperties($vendorPackages);
        self::loadUserConfiguration($vendorPackages);
        self::loadProjectConfiguration($vendorPackages);
    }

    /**
     * @param array $vendorPackages
     */
    private static function projectProperties(array $vendorPackages)
    {
        $git = new GitWorkingCopy(new GitWrapper(), self::getPaths()->getProjectRootDirectory());
        $remote = $git->getRemotes();
        self::$project = new GitProject();
        self::$project->parse($remote['origin']['push']);
    }

    /**
     * @param array $vendorPackages
     */
    private static function loadUserConfiguration(array $vendorPackages)
    {
        self::$userConfiguration = new UserConfiguration();
        $configurationFile = $_SERVER['HOME'] . '/.t3build';
        if (is_file($configurationFile)) {
            self::$userConfiguration->addConfiguration(Yaml::parse(file_get_contents($configurationFile)));
        }
    }

    /**
     * @param array $vendorPackages
     */
    private static function loadProjectConfiguration(array $vendorPackages)
    {
        $configurationDirectories = self::loadConfigurationDirectories($vendorPackages);
        self::$projectConfiguration = self::loadConfigurationFiles($configurationDirectories);
    }

    /**
     * @param array $vendorPackages
     * @return array
     */
    private static function loadConfigurationDirectories(array $vendorPackages): array
    {
        $t3buildConfiguration = self::getPaths()->getT3BuildSourceDirectory() . '/configuration';
        $selfConfiguration1 = self::getPaths()->getProjectRootDirectory() . '/configuration';
        $selfConfiguration2 = self::getPaths()->getProjectRootDirectory() . '/config';

        $configurationDirectories = [];
        $configurationDirectories[] = $t3buildConfiguration;

        foreach ($vendorPackages as $package) {
            if ($package->getType() === 't3build-package') {
                $configurationPackagePath = $package->getRootDirectory() . '/configuration';
                if (is_dir($configurationPackagePath)) {
                    $configurationDirectories[] = $configurationPackagePath;
                }
            }
        }

        foreach ($vendorPackages as $package) {
            if ($package->getType() === 't3build-configuration') {
                $configurationPackagePath = $package->getRootDirectory() . '/configuration';
                if (is_dir($configurationPackagePath)) {
                    $configurationDirectories[] = $configurationPackagePath;
                }
            }
        }

        if (is_dir($selfConfiguration1) && $selfConfiguration1 !== $t3buildConfiguration) {
            $configurationDirectories[] = $selfConfiguration1;
        }

        if (is_dir($selfConfiguration2) && $selfConfiguration2 !== $t3buildConfiguration) {
            $configurationDirectories[] = $selfConfiguration2;
        }

        return $configurationDirectories;
    }

    /**
     * @param array $configurationDirectories
     * @return ProjectConfiguration
     */
    private static function loadConfigurationFiles(array $configurationDirectories): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();
        foreach ($configurationDirectories as $configurationDirectory) {
            if (is_string($configurationDirectory) && is_dir($configurationDirectory)) {
                $configurationFiles = FileDirectoryUtility::getFilesInDir($configurationDirectory, 'yaml', true);
                foreach ($configurationFiles as $configurationFile) {
                    $projectConfiguration->addConfigurationYaml(file_get_contents($configurationFile));
                }
            }
        }
        return $projectConfiguration;
    }

    /**
     * @return ProjectConfiguration
     */
    public static function getProjectConfiguration(): ProjectConfiguration
    {
        return self::$projectConfiguration;
    }

    /**
     * @return UserConfiguration
     */
    public static function getUserConfiguration(): UserConfiguration
    {
        return self::$userConfiguration;
    }

    /**
     * @return GitProject
     */
    public static function getProject(): GitProject
    {
        return self::$project;
    }

    /**
     * @return Paths
     */
    public static function getPaths(): Paths
    {
        return self::$paths;
    }
}
