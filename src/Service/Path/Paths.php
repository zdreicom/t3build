<?php
declare(strict_types=1);

namespace Z3\T3build\Service\Path;

use Z3\T3build\Service\Config;
use Z3\T3buildNode\Service\Path\NodePaths;

class Paths
{

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var string
     */
    protected $projectRootDirectory = '';

    /**
     * @var string
     */
    protected $projectVendorDirectory = '';

    /**
     * @var string
     */
    protected $projectBinDirectory = '';

    /**
     * @var string
     */
    protected $projectWebDirectory = '';

    /**
     * @var string
     */
    protected $projectPackagesDirectory = '';

    /**
     * @var string
     */
    protected $projectTemporaryDirectory = '';

    /**
     * @var string
     */
    protected $projectArtifactDirectory = '';

    /**
     * @var string
     */
    protected $workingRootDirectory = '';

    /**
     * @var string
     */
    protected $workingVendorDirectory = '';

    /**
     * @var string
     */
    protected $workingBinDirectory = '';

    /**
     * @var string
     */
    protected $workingPackagesDirectory = '';

    /**
     * @var string
     */
    protected $workingWebDirectory = '';

    /**
     * @var string
     */
    protected $t3buildRootDirectory = '';

    /**
     * @var string
     */
    protected $t3buildVendorDirectory = '';

    /**
     * @var string
     */
    protected $t3buildBinDirectory = '';

    /**
     * @var string
     */
    protected $t3BuildSourceDirectory = '';

    /**
     * @param string $projectDirectory
     * @param $composerConfig
     * @param string $type
     */
    public function preInitPaths(string $projectDirectory, $composerConfig, string $type)
    {
        $this->type = $type;
        $this->loadWorkingPathsFromComposer($projectDirectory, $composerConfig);

        $this->projectRootDirectory = $this->workingRootDirectory;
        $this->projectVendorDirectory = $this->workingVendorDirectory;
        $this->projectBinDirectory = $this->workingBinDirectory;
        $this->projectWebDirectory = $this->workingWebDirectory;

        $this->t3buildRootDirectory = $this->workingRootDirectory;
        $this->t3buildVendorDirectory = $this->workingVendorDirectory;
        $this->t3buildBinDirectory = $this->workingBinDirectory;

        $this->setT3BuildPaths();
    }

    /**
     * @param string $projectDirectory
     * @param $composerConfig
     * @param string $type
     */
    public function postInitPackagesPaths()
    {
        $this->projectPackagesDirectory = $this->projectRootDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/packages/path', 'packages');
        $this->projectTemporaryDirectory = $this->projectRootDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/temporary/path', 'temporary');
        $this->projectArtifactDirectory = $this->projectRootDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/artifact/path', 'artifact');

        if ($this->type === 'self') {
            $this->projectPackagesDirectory = $this->projectRootDirectory . '/tests/Fixtures/Dummys';
        }
        $this->workingPackagesDirectory = $this->projectPackagesDirectory;
    }

    /**
     * @param string $workingRootDirectory
     * @param $composerConfig
     */
    private function loadWorkingPathsFromComposer(string $workingRootDirectory, $composerConfig)
    {
        $this->workingRootDirectory = $workingRootDirectory;
        $this->workingVendorDirectory = $this->workingRootDirectory . '/vendor';
        $this->workingBinDirectory = $this->workingRootDirectory . '/vendor/bin';
        $this->workingWebDirectory = $this->workingRootDirectory;

        if (property_exists($composerConfig, 'config')) {
            $config = $composerConfig->config;
            if (property_exists($config, 'vendor-dir')) {
                $this->workingVendorDirectory = $this->workingRootDirectory . '/' . $config->{'vendor-dir'};
            }
            if (property_exists($config, 'bin-dir')) {
                $this->workingBinDirectory = $this->workingRootDirectory . '/' . $config->{'bin-dir'};
            }
        }
        if (property_exists($composerConfig, 'extra')) {
            $extra = $composerConfig->extra;
            if (property_exists($extra, 'typo3/cms')) {
                $extraTypo3 =  $extra->{'typo3/cms'};
                if (property_exists($extraTypo3, 'web-dir')) {
                    $this->workingWebDirectory = $this->workingRootDirectory . '/' . $extraTypo3->{'web-dir'};
                }
            }
        }
    }

    /**
     * @return void
     */
    private function setT3BuildPaths()
    {
        $this->t3BuildSourceDirectory = $this->t3buildVendorDirectory . '/z3/t3build';
        if ($this->type === 'self') {
            $this->t3BuildSourceDirectory = $this->t3buildRootDirectory;
        }
    }

    /**
     * @param string $workingRootDirectory
     * @param $composerConfig
     */
    public function switchWorkingDirectory(string $workingRootDirectory, $composerConfig)
    {
        $this->loadWorkingPathsFromComposer($workingRootDirectory, $composerConfig);
        $this->workingPackagesDirectory = $this->workingRootDirectory . '/' . Config::getProjectConfiguration()->getConfigurationString('workingDirectories/packages/path', 'packages');
        $this->addTypo3Path();
    }

    private function addTypo3Path()
    {
        /** @toDo: Load web dir from composer.json */
        putenv('TYPO3_PATH_COMPOSER_ROOT=' . $this->workingRootDirectory);
        $_ENV['TYPO3_PATH_COMPOSER_ROOT'] = $this->workingRootDirectory;

        putenv('TYPO3_PATH_APP=' . $this->workingRootDirectory);
        $_ENV['TYPO3_PATH_APP'] = $this->workingRootDirectory;

        putenv('TYPO3_PATH_ROOT=' . $this->workingRootDirectory . '/web');
        $_ENV['TYPO3_PATH_ROOT'] =  $this->workingRootDirectory . '/web';

        putenv('TYPO3_PATH_WEB=' . $this->workingRootDirectory . '/web');
        $_ENV['TYPO3_PATH_WEB'] = $this->workingRootDirectory . '/web';
    }

    /**
     * @return string
     */
    public function getProjectRootDirectory(): string
    {
        return $this->projectRootDirectory;
    }

    /**
     * @return string
     */
    public function getProjectVendorDirectory(): string
    {
        return $this->projectVendorDirectory;
    }

    /**
     * @return string
     */
    public function getProjectBinDirectory(): string
    {
        return $this->projectBinDirectory;
    }

    /**
     * @return string
     */
    public function getProjectPackagesDirectory(): string
    {
        return $this->projectPackagesDirectory;
    }

    /**
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function getProjectTemporaryDirectory(string $type = ''): string
    {
        $temporaryDirectory = $this->projectTemporaryDirectory;
        if (!@mkdir($temporaryDirectory, 0777, true) && !is_dir($temporaryDirectory)) {
            throw new \Exception('Can not create temp directory in: ' . $temporaryDirectory);
        }
        if (strlen($type) > 0) {
            $temporaryDirectory .= '/' . $type;
            if (!@mkdir($temporaryDirectory, 0777, true) && !is_dir($temporaryDirectory)) {
                throw new \Exception('Can not create temp directory in: ' . $temporaryDirectory);
            }
        }
        return $temporaryDirectory;
    }

    /**
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function getProjectArtifactDirectory(string $type = ''): string
    {
        $artifactDirectory = $this->projectRootDirectory . '/artifacts';
        if (!@mkdir($artifactDirectory, 0777, true) && !is_dir($artifactDirectory)) {
            throw new \Exception('Can not create artifact directory in: ' . $artifactDirectory);
        }
        if (strlen($type) > 0) {
            $artifactDirectory .= '/' . $type;
            if (!@mkdir($artifactDirectory, 0777, true) && !is_dir($artifactDirectory)) {
                throw new \Exception('Can not create directory in: ' . $artifactDirectory);
            }
        }
        return $artifactDirectory;
    }

    /**
     * @return string
     */
    public function getWorkingRootDirectory(): string
    {
        return $this->workingRootDirectory;
    }

    /**
     * @return string
     */
    public function getWorkingVendorDirectory(): string
    {
        return $this->workingVendorDirectory;
    }

    /**
     * @return string
     */
    public function getWorkingBinDirectory(): string
    {
        return $this->workingBinDirectory;
    }

    public function getWorkingWebDirectory(): string
    {
        return $this->workingWebDirectory;
    }

    /**
     * @return string
     */
    public function getWorkingPackagesDirectory(): string
    {
        return $this->workingPackagesDirectory;
    }

    /**
     * @return string
     */
    public function getT3buildRootDirectory(): string
    {
        return $this->t3buildRootDirectory;
    }

    /**
     * @return string
     */
    public function getT3buildVendorDirectory(): string
    {
        return $this->t3buildVendorDirectory;
    }

    /**
     * @return string
     */
    public function getT3buildBinDirectory(): string
    {
        return $this->t3buildBinDirectory;
    }

    /**
     * @return string
     */
    public function getT3BuildSourceDirectory(): string
    {
        return $this->t3BuildSourceDirectory;
    }

    /**
     * @param $type
     * @return string
     * @throws \Exception
     */
    public function getT3BuildTemporaryDirectory($type = ''): string
    {
        $temporaryDirectory = $this->getProjectTemporaryDirectory('t3build');
        if (strlen($type) > 0) {
            $temporaryDirectory .= '/' . $type;
            if (!@mkdir($temporaryDirectory, 0777, true) && !is_dir($temporaryDirectory)) {
                throw new \Exception('Can not create temp directory in: ' . $temporaryDirectory);
            }
        }
        return $temporaryDirectory;
    }

    public function initNodePaths()
    {
        NodePaths::initNode();
    }

    /**
     * @return string
     */
    public function getNodeRootDirectory(): string
    {
        return NodePaths::getNodeRootDirectory();
    }

    /**
     * @return string
     */
    public function getNodeBinDirectory(): string
    {
        return NodePaths::getNodeBinDirectory();
    }

    /**
     * @return string
     */
    public function getNodeExecutable(): string
    {
        return NodePaths::getNodeExecutable();
    }

    /**
     * @return string
     */
    public function getNpmExecutable(): string
    {
        return NodePaths::getNpmExecutable();
    }

    /**
     * @param string $comamndName
     * @return string
     */
    public function getNodeCommand(string $comamndName): string
    {
        return $this->getNodeExecutable() . ' ' . $this->getNodeBinDirectory() . '/' . $comamndName;
    }
}
