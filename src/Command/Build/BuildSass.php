<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Build;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Build\BuildSass as BuildSassJob;

class BuildSass extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('build:sass')
            // the short description shown while running "php bin/console list"
            ->setDescription('Builds the Css files from the Sass files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new BuildSassJob();
        $job->run($input, $output);
    }
}
