<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Deploy;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Command\AbstractCommand;
use Z3\T3build\Job\Config\MakeAndSetKey;

class MakeSSHKey extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('deploy:makesshkey')
            // the short description shown while running "php bin/console list"
            ->setDescription('Make and set deploy key');

        $this->addArgument('environment', InputArgument::REQUIRED, 'The environment which should be configured (production,staging)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $job = new MakeAndSetKey();
        $job->run($input, $output, [
            'environment' => $environment
        ]);
    }
}
