<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Lint;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Lint\LintSass as LintSassJob;

class LintSass extends Command
{
    protected function configure()
    {
        $this->task = 'lint';
        $this->type = 'sass';

        $this->needInputPath = true;
        $this->needConfigFile= true;

        $this
            // the name of the command (the part after "bin/console")
            ->setName($this->task . ':' . $this->type)
            // the short description shown while running "php bin/console list"
            ->setDescription('Lints the Sass files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new LintSassJob();
        $job->run($input, $output);
    }
}
