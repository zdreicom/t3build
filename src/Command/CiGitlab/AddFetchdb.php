<?php
declare(strict_types=1);

namespace Z3\T3build\Command\CiGitlab;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Service\Configuration\Configuration;

class AddFetchdb extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('ci-gitlab:add-fetchdb')
            // the short description shown while running "php bin/console list"
            ->setDescription('Add the default .gitlab-ci.yml in root directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function addConfigurationOverride(InputInterface $input, OutputInterface $output) : array
    {
        return [
            'fetchdb_production' => Configuration::getConfigurationArray('ci/gitlab/fetchdb')
        ];
    }
}
