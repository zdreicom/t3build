<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Time;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class TimeStart extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'helper';
    }

    public function getJobTask(): string
    {
        return 'commit';
    }

    public function getJobType(): string
    {
        return 'git';
    }

    public function isMultiJob(): bool
    {
        return false;
    }

    public function isNodeJob(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Commit to GIT';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @return int|null
     */
    public function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = []): ?int
    {
        $this->setSpeendTime(time());
        return null;
    }

    /**
     * @param int $time
     */
    private function setSpeendTime(int $time)
    {
        Config::getUserConfiguration()->setConfigurationInt('git/lastcommit', $time);
        Config::getUserConfiguration()->save();
    }
}
