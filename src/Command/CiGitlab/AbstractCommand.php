<?php
declare(strict_types=1);

namespace Z3\T3build\Command\CiGitlab;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Command\AbstractCommand as BaseAbstractCommand;
use Z3\T3build\Service\Configuration\Configuration;
use Z3\T3build\Service\Path\BasePaths;

abstract class AbstractCommand extends BaseAbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gitlabCiYmlFile = BasePaths::getRootDirectory() . '/.gitlab-ci.yml';
        if (!@is_file($gitlabCiYmlFile)) {
            throw new \Error('The file <info>.gitlab-ci.yml</info> dose not exists pleas use ci-gitlab:init to creaed it');
        }
        $currentConfiguration = Yaml::parse(file_get_contents($gitlabCiYmlFile));

        $newConfiguration = $currentConfiguration;

        $addConfigurationOverride = $this->addConfigurationOverride($input, $output);
        foreach ($addConfigurationOverride as $path => $value) {
            $newConfiguration =Configuration::addOrOverrideToArray($path, $newConfiguration, $value);
        }

        $addConfigurationMergeRecursive = $this->addConfigurationMergeRecursive($input, $output);
        $newConfiguration = Configuration::arrayMergeRecursive($newConfiguration, $addConfigurationMergeRecursive);

        file_put_contents($gitlabCiYmlFile, Yaml::dump($newConfiguration, 4));
        $output->writeln('<info>✔ New .gitlab-ci.yml</info> written');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function addConfigurationMergeRecursive(InputInterface $input, OutputInterface $output) : array
    {
        return [];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function addConfigurationOverride(InputInterface $input, OutputInterface $output) : array
    {
        return [];
    }
}
