<?php
declare(strict_types=1);

namespace Z3\T3build\Command\CiGitlab;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Command\AbstractCommand as BaseAbstractCommand;
use Z3\T3build\Service\Configuration\Configuration;
use Z3\T3build\Service\Path\BasePaths;

class Init extends BaseAbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('ci-gitlab:init')
            // the short description shown while running "php bin/console list"
            ->setDescription('Add the default .gitlab-ci.yml in root directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $defaultGitlabCi = Configuration::getConfigurationArray('ci/gitlab/default');
        $gitlabCiYmlFile = BasePaths::getRootDirectory() . '/.gitlab-ci.yml';
        if (is_file($gitlabCiYmlFile)) {
            throw new \Error('The file <info>.gitlab-ci.yml</info> already exists');
        }
        file_put_contents($gitlabCiYmlFile, Yaml::dump($defaultGitlabCi));
        $output->writeln('<info>✔ .gitlab-ci.yml</info> created');
    }
}
