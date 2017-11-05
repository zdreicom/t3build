<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Config;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Service\Config;

class SetToken extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('git:settoken')
            // the short description shown while running "php bin/console list"
            ->setDescription('Set the GitLab token');

        $this
            ->addArgument('host', InputArgument::REQUIRED, 'The host of the GitLab server')
            ->addArgument('token', InputArgument::REQUIRED, 'The GitLab API token');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        $token = $input->getArgument('token');
        Config::getUserConfiguration()->setConfigurationString('git/gitlab/' . $host . '/token', $token);
        Config::getUserConfiguration()->save();
    }
}
