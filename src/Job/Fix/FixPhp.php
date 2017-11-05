<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Fix;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class FixPhp extends AbstractJob
{
    public function getJobClass(): string
    {
        return '';
    }

    public function getJobTask(): string
    {
        return 'fix';
    }

    public function getJobType(): string
    {
        return 'php';
    }

    public function getDescription(): string
    {
        return 'Fixes the PHP files';
    }

    public function isMultiJob(): bool
    {
        return true;
    }

    public function needInputPath(): bool
    {
        return true;
    }

    public function needConfigFile(): bool
    {
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $processString = Config::getPaths()->getT3buildBinDirectory() . '/php-cs-fixer fix'
            . ' --config=' . $this->configFile . ' -v --using-cache=no';

        $process = new Process($processString);
        $process->mustRun();
    }
}
