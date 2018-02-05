<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Deploy;

use GuzzleHttp\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Config;

class Deploy extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'deploy';
    }

    public function getJobTask(): string
    {
        return 'deploy';
    }

    public function getJobType(): string
    {
        return 'deployer';
    }

    public function getDescription(): string
    {
        return 'Deploy to Server';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $type = Bootstrap::getRootPackage()->getType();

        $environment = $arguments['environment'];
        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        $user = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/user', '');
        $projectPath = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/path', '');

        Config::getProjectConfiguration()->setTage($type);
        $sharedDirs = Config::getProjectConfiguration()->getConfigurationArray('deployer/shared-dirs', []);
        $sharedFiles = Config::getProjectConfiguration()->getConfigurationArray('deployer/shared-files', []);
        $writableDirs = Config::getProjectConfiguration()->getConfigurationArray('deployer/writable-dirs', []);
        Config::getProjectConfiguration()->resetTags();

        $config = $this->getDummy($host, $user, $projectPath, $sharedDirs, $sharedFiles, $writableDirs);
        $deployFile = Config::getPaths()->getProjectRootDirectory() . '/deploy.php';
        file_put_contents($deployFile, $config);
        $processString  = Config::getPaths()->getT3buildBinDirectory() . '/dep deploy';
        passthru($processString);

        switch ($type) {
            case 'typo3-cms':
                $this->postJobTypo3($input, $output, $arguments);
                break;
            case 'neos-flow':
                $this->postJobNeos($input, $output, $arguments);
                break;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    private function postJobTypo3(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $arguments['environment'];
        $php = Config::getProjectConfiguration()->getConfigurationArray('deployer/php', 'php');

        $publicUrl = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/publicUrl', '');
        if (strlen($publicUrl) > 0) {
            $client = new Client();
            $res = $client->get($publicUrl . '/opcache_reset.php');
        }
        $this->executeRemoteCommand($environment, $php . ' bin/typo3cms database:updateschema');
        $this->executeRemoteCommand($environment, $php . ' bin/typo3cms cache:flush');
        $this->executeRemoteCommand($environment, $php . ' bin/typo3cms database:updateschema');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    private function postJobNeos(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $arguments['environment'];
        $php = Config::getProjectConfiguration()->getConfigurationArray('deployer/php', 'php');

        $this->executeRemoteCommand($environment, 'rm -rf Data/Temporary');
        $this->executeRemoteCommand($environment, $php . ' ./flow flow:cache:flush');
        $this->executeRemoteCommand($environment, $php . ' ./flow doctrine:update');
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $projectPath
     * @param array $sharedDirs
     * @param array $sharedFiles
     * @param array $writableDirs
     * @return string
     */
    private function getDummy(string $host, string $user, string $projectPath, array $sharedDirs, array $sharedFiles, array $writableDirs) : string
    {
        $dummy = file_get_contents(Config::getPaths()->getT3BuildSourceDirectory() . '/res/ConfigurationTemplates/Deployer.php');
        $dummy = str_replace('<HOST>', $host, $dummy);
        $dummy = str_replace('<USER>', $user, $dummy);
        $dummy = str_replace('<PROJECT_PATH>', $projectPath, $dummy);
        $dummy = str_replace('<SOURCE>', Config::getPaths()->getProjectArtifactDirectory('build'), $dummy);
        $dummy = str_replace('<DEPLOYER_PATH>', Config::getPaths()->getT3buildVendorDirectory() . '/deployer/deployer', $dummy);
        $dummy = str_replace('<DEPLOYER_RSYNC_PATH>', Config::getPaths()->getT3buildVendorDirectory() . '/deployer/recipes', $dummy);

        $dummy = str_replace('<SHARED_DIRS>', var_export($sharedDirs, true), $dummy);
        $dummy = str_replace('<SHARED_FILES>', var_export($sharedFiles, true), $dummy);
        $dummy = str_replace('<WRITABLE_DIRS>', var_export($writableDirs, true), $dummy);

        return $dummy;
    }
}
