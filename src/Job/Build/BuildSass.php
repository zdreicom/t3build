<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Build;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;
use Z3\T3build\Utility\FileDirectoryUtility;

class BuildSass extends AbstractJob
{
    public function getJobClass(): string
    {
        return '';
    }

    public function getJobTask(): string
    {
        return 'build';
    }

    public function getJobType(): string
    {
        return 'sass';
    }

    public function getDescription(): string
    {
        return 'Builds the Css files from the Sass files';
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

    public function needOutputPath(): bool
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
        $scssFiles = FileDirectoryUtility::getFilesInDir($this->path->getInputPath(), 'scss');

        foreach ($scssFiles as $scssFile) {
            $cssFile = substr($scssFile, 0, -4) . 'css';
            $sassCompiler = Config::getPaths()->getNodeCommand('node-sass');
            $processString  = $sassCompiler . ' ' . $this->path->getInputPath() . '/' . $scssFile . ' ' . $this->path->getOutputPath() . '/' . $cssFile;
            $process = new Process($processString);
            $process->mustRun();
        }
    }
}
