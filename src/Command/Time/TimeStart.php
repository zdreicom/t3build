<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Time;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Time\TimeStart as TimeStartJob;

class TimeStart extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('time:start')
            // the short description shown while running "php bin/console list"
            ->setDescription('Set current time as start');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timeStartJob = new TimeStartJob();
        $timeStartJob->run($input, $output);
    }
}
