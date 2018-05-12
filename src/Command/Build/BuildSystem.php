<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Build;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Build\System\Neos as NeosBuildSystemJob;
use Z3\T3build\Job\Build\System\Typo3 as Typo3BuildSystemJob;
use Z3\T3build\Service\Bootstrap;

class BuildSystem extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('build:system')
            // the short description shown while running "php bin/console list"
            ->setDescription('Build the System');
        $this->addArgument('versionName', InputArgument::OPTIONAL, 'The name of the version', '');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = Bootstrap::getRootPackage()->getType();
        switch ($type) {
            case 'typo3-cms':
                $job = new Typo3BuildSystemJob();
                $job->run($input, $output);
                break;
            case 'neos-flow':
                $job = new NeosBuildSystemJob();
                $job->run($input, $output);
                break;
        }
    }
}
