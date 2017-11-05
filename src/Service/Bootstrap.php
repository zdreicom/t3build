<?php
declare(strict_types=1);

namespace Z3\T3build\Service;

use Symfony\Component\Console\Application;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Service\Path\BasePaths;
use Z3\T3build\Utility\FileDirectoryUtility;

class Bootstrap
{
    /**
     * @var \Z3\T3build\Domain\Model\Package $rootPackage
     */
    protected static $rootPackage = null;

    /**
     * @var \Z3\T3build\Domain\Model\Package[]
     */
    protected $vendorPackages = [];

    /**
     * @var string
     */
    protected $packageDirectory = '';

    /**
     * @var string[]
     */
    protected $configurationDirectories = [];

    /**
     * @var string
     */
    protected $composerConfig = '';

    public function run()
    {
        $this->loadRootPackage();
        $this->loadPaths();
        $this->loadVendorPackages();
        $this->loadConfiguration();
        $this->loadPathsPost();

        /*
        echo Config::getPaths()->getProjectRootDirectory() . "\n";
        echo Config::getPaths()->getProjectVendorDirectory() . "\n";
        echo Config::getPaths()->getProjectBinDirectory() . "\n";
        echo Config::getPaths()->getProjectPackagesDirectory() . "\n\n";

        echo Config::getPaths()->getWorkingRootDirectory() . "\n";
        echo Config::getPaths()->getWorkingVendorDirectory() . "\n";
        echo Config::getPaths()->getWorkingBinDirectory() . "\n";
        echo Config::getPaths()->getWorkingPackagesDirectory() . "\n\n";

        echo Config::getPaths()->getT3buildRootDirectory() . "\n";
        echo Config::getPaths()->getT3buildVendorDirectory() . "\n";
        echo Config::getPaths()->getT3buildBinDirectory() . "\n";
        echo Config::getPaths()->getT3buildTempDirectory() . "\n";
        echo Config::getPaths()->getT3BuildSourceDirectory() . "\n\n";
        */
    }

    /**
     * @param string $workingDirectory
     */
    public static function switchWorkingDirectory(string $workingDirectory)
    {
        $rootDirectory = getcwd();
        /**
         * The old Way
         */
        $composerConfig = self::loadRootPackageStatic($workingDirectory);
        BasePaths::initWorkingDirectory($workingDirectory, $composerConfig);
        BasePaths::postInitPackagesDirectory();

        /**
         * The new way
         */
        $composerConfig = self::loadRootPackageStatic($workingDirectory);
        Config::getPaths()->switchWorkingDirectory($workingDirectory, $composerConfig);

        self::$rootPackage->loadPaths();
    }

    private function loadRootPackage()
    {
        $this->composerConfig = self::loadRootPackageStatic(getcwd());
    }

    /**
     * @param string $workingDirectory
     * @return void
     */
    private static function loadRootPackageStatic(string $workingDirectory)
    {
        $rootDirectory = $workingDirectory;
        $composerJsonPath = $rootDirectory . '/composer.json';

        if (!is_file($composerJsonPath)) {
            throw new \UnexpectedValueException('No composer.json in root path: ' . $workingDirectory);
        }

        $composerConfig = json_decode(file_get_contents($composerJsonPath));
        if ($composerConfig === null) {
            throw new \UnexpectedValueException('Could not parse composer.json.');
        }

        self::setRootPackage(new Package($rootDirectory, $composerConfig));
        return $composerConfig;
    }

    private function loadPaths()
    {
        $rootDirectory = getcwd();
        /**
         * The old Way
         */
        BasePaths::init($rootDirectory, $this->composerConfig, self::$rootPackage->getType());

        /**
         * The new way
         */
        $projectDirectory = getcwd();
        Config::preInitPaths($projectDirectory, $this->composerConfig, self::$rootPackage->getType());
    }

    private function loadVendorPackages()
    {
        $packagePaths = FileDirectoryUtility::getDirectoriesInPath(Config::getPaths()->getProjectVendorDirectory(), true, 1, 1);
        foreach ($packagePaths as $packagePath) {
            $composerConfig = FileDirectoryUtility::getComposerConfig($packagePath);
            if ($composerConfig) {
                $this->vendorPackages[] = new Package($packagePath, $composerConfig);
            }
        }
    }

    private function loadConfiguration()
    {
        Config::init($this->vendorPackages);
    }

    private function loadPathsPost()
    {
        $rootDirectory = getcwd();
        /**
         * The old Way
         */
        BasePaths::postInitPackagesDirectory();
        BasePaths::postInitTempDirectory();

        /**
         * The new way
         */
        Config::getPaths()->postInitPackagesPaths();

        self::$rootPackage->loadPaths();
    }

    /**
     * @param string $type
     */
    public function loadApplication(string $type)
    {
        $application = new Application();

        switch ($type) {
            case 'setup':
                $application->add(new \Z3\T3build\Command\Deploy\SetHost());
                $application->add(new \Z3\T3build\Command\Deploy\MakeSSHKey());

                break;
            case 'build':
                $application->add(new \Z3\T3build\Command\Show\ShowDirectories());
                $application->add(new \Z3\T3build\Command\Show\ShowPackages());
                $application->add(new \Z3\T3build\Command\Show\ShowPackagePaths());

                $application->add(new \Z3\T3build\Command\Lint\LintComposer());
                $application->add(new \Z3\T3build\Command\Lint\LintSass());
                $application->add(new \Z3\T3build\Command\Lint\CodeStylePhp());
                $application->add(new \Z3\T3build\Command\Lint\LintTypeScript());

                $application->add(new \Z3\T3build\Command\Fix\FixSass());
                $application->add(new \Z3\T3build\Command\Fix\FixPhp());
                $application->add(new \Z3\T3build\Command\Fix\FixTypeScript());

                $application->add(new \Z3\T3build\Command\Build\BuildSass());
                $application->add(new \Z3\T3build\Command\Build\BuildTypeScript());
                $application->add(new \Z3\T3build\Command\Build\BuildSystem());

                break;
            case 'deploy':
                $application->add(new \Z3\T3build\Command\Deploy\Deploy());
                break;
            case 'config':
                $application->add(new \Z3\T3build\Command\Config\SetToken());
                break;
            case 'ci':
                $application->add(new \Z3\T3build\Command\Lint\LintComposer());
                $application->add(new \Z3\T3build\Command\Lint\LintSass());
                $application->add(new \Z3\T3build\Command\Lint\CodeStylePhp());
                $application->add(new \Z3\T3build\Command\Lint\LintTypeScript());

                $application->add(new \Z3\T3build\Command\Build\BuildSass());
                $application->add(new \Z3\T3build\Command\Build\BuildTypeScript());

                $application->add(new \Z3\T3build\Command\Deploy\SetTemporarySSHKey());
                $application->add(new \Z3\T3build\Command\Ci\ClearOpcache());
                break;
            case 'fetch':
                $application->add(new \Z3\T3build\Command\Fetch\Database());
                $application->add(new \Z3\T3build\Command\Fetch\Files());
                $application->add(new \Z3\T3build\Command\Database\CopyRemote());
                break;
            default:
        }

        $application->run();
    }

    /**
     * @param Package $rootPackage
     */
    protected static function setRootPackage(Package $rootPackage)
    {
        self::$rootPackage = $rootPackage;
    }

    /**
     * @return Package
     */
    public static function getRootPackage(): Package
    {
        return self::$rootPackage;
    }
}
