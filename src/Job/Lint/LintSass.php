<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Lint;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3buildNode\Service\Path\NodePaths;

class LintSass extends AbstractJob
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
        return 'sass';
    }

    public function getDescription(): string
    {
        return 'Lint the Sass files';
    }

    public function isMultiJob(): bool
    {
        return true;
    }

    public function isNodeJob(): bool
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
        $sassLinter = NodePaths::getNodeExecutable() . ' ' . NodePaths::getNodeBinDirectory() . '/stylelint';

        $processString  = $sassLinter . ' "' . $this->inputPath . '/**/*.scss' . '"';
        $processString  .= ' --config ' . $this->configFile;
        $process = new Process($processString);
        $process->mustRun();

        return null;
    }
}
