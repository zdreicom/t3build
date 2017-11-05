<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Fix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Fix\FixPhp as FixPhpJob;

class FixPhp extends Command
{
    protected function configure()
    {
        $this->task = 'fix';
        $this->type = 'php';

        $this
            // the name of the command (the part after "bin/console")
            ->setName('fix:php')
            // the short description shown while running "php bin/console list"
            ->setDescription('Fixes the PHP files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixPhpJob = new FixPhpJob();
        $fixPhpJob->run($input, $output);
    }
}
