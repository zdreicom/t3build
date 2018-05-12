<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Build\System;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Job\Build\BuildSass;
use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Config;

class Neos extends AbstractJob
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
        return 'system';
    }

    public function getDescription(): string
    {
        return 'Build the NEOS system';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @return int|null
     * @throws \Exception
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = []): ?int
    {
        $buildDirectory = Config::getPaths()->getT3BuildTemporaryDirectory() . '/build';

        // Copy Project in Temp
        $output->writeln('➤ Copy project in temp build');
        $processString = 'git clone ' . Config::getPaths()->getProjectRootDirectory() . '/.git ' . $buildDirectory;
        $process = new Process($processString);
        $process->mustRun();
        $output->writeln("\r\033[K\033[1A\r<info>✔</info>");

        // Composer Setup
        $output->writeln('➤ Composer install without dev');
        $processString = 'composer install --no-dev';
        $process = new Process($processString, $buildDirectory);
        $process->mustRun();
        $output->writeln("\r\033[K\033[1A\r<info>✔</info>");

        Bootstrap::switchWorkingDirectory($buildDirectory);

        // Build Css Files
        $buildSassJob = new BuildSass();
        $buildSassJob->run($input, $output);

        // Move to
        $artifacts = Config::getPaths()->getProjectArtifactDirectory();
        $output->writeln('➤ Move to artifacts');
        $processString = 'mv ' . $buildDirectory . ' ' . $artifacts . '/build';
        $process = new Process($processString);
        $process->mustRun();
        $output->writeln("\r\033[K\033[1A\r<info>✔</info>");

        return null;
    }
}
