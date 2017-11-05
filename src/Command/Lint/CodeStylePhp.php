<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Lint;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Lint\LintPhp as LintPhpJob;

class CodeStylePhp extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('cs:php')
            // the short description shown while running "php bin/console list"
            ->setDescription('Checks the code style of the PHP files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lintPhpJob = new LintPhpJob();
        $lintPhpJob->run($input, $output);
    }
}
