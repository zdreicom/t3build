<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Lint;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class LintPhp extends AbstractJob
{
    public function getJobClass(): string
    {
        return '';
    }

    public function getJobTask(): string
    {
        return 'lint';
    }

    public function getJobType(): string
    {
        return 'php';
    }

    public function getDescription(): string
    {
        return 'Checks the code style of the PHP files';
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
     * @return int|null
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = []): ?int
    {
        $processString = Config::getPaths()->getT3buildBinDirectory() . '/php-cs-fixer fix'
            . ' --config=' . $this->configFile . ' -v --dry-run --using-cache=no';

        $process = new Process($processString);
        $process->mustRun();

        return null;
    }
}
