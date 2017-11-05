<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Deploy;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Command\AbstractCommand;
use Z3\T3build\Job\Deploy\Deploy as DeployJob;

class Deploy extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('deploy:deploy')
            // the short description shown while running "php bin/console list"
            ->setDescription('Deploy the system on the server');

        $this->addArgument('environment', InputArgument::REQUIRED, 'The environment which should be configured (production,staging)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $job = new DeployJob();
        $job->run($input, $output, [
            'environment' => $environment
        ]);
    }
}
