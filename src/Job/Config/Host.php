<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;
use Z3\T3build\Service\Configuration\Configuration;

class Host extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'config';
    }

    public function getJobTask(): string
    {
        return 'host';
    }

    public function getJobType(): string
    {
        return '';
    }

    public function isMultiJob(): bool
    {
        return false;
    }

    public function isNodeJob(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Set host config for Mittwald';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @throws \Exception
     * @return void
     */
    public function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $arguments['environment'];
        $account = $arguments['account'];

        $projectConfigurationDirectory = Config::getPaths()->getProjectRootDirectory() . '/configuration';

        if (!@mkdir($projectConfigurationDirectory) && !is_dir($projectConfigurationDirectory)) {
            throw new \Exception('Can not create configuration folder');
        }
        $localConfigurationFile = $projectConfigurationDirectory . '/Deploy.yaml';

        $localConfiguration = new Configuration();

        if (is_file($localConfigurationFile)) {
            $localConfiguration->addConfiguration(Yaml::parse(file_get_contents($localConfigurationFile)));
        }

        $gitProject = Config::getProject();

        $localConfiguration->setConfigurationString('deploy/' . $environment . '/host', $account . '.mittwaldserver.info');
        $localConfiguration->setConfigurationString('deploy/' . $environment . '/user', $account);
        $localConfiguration->setConfigurationString('deploy/' . $environment . '/path', '/home/www/' . $account . '/html/' . $environment . '.' . $gitProject->getFullName());
        file_put_contents($localConfigurationFile, $localConfiguration->exportConfigurationYaml());
    }
}
