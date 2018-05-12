<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Build\System;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Job\Build\BuildSass;
use Z3\T3build\Job\Version\Set;
use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Config;

class Typo3 extends AbstractJob
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
        return 'Build the TYPO3 system';
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

        Bootstrap::switchWorkingDirectory($buildDirectory);

        $versionName =  $input->getArgument('versionName');
        if ($versionName !== '') {
            $versionSet = new Set();
            $versionSet->runSingleJob($input, $output, []);
        }

        // Composer Setup
        $output->writeln('➤ Composer install without dev');
        $processString = 'composer install --no-dev';
        $process = new Process($processString, $buildDirectory);
        $process->mustRun();
        $output->writeln("\r\033[K\033[1A\r<info>✔</info>");

        // Add opcache_reset() reset file
        $workingWebDirectory = Config::getPaths()->getWorkingWebDirectory();
        $fileSystem = new Filesystem();
        $opcacheReset = '<?php if(function_exists("opcache_reset")) {opcache_reset(); echo "opcache reset";} else {echo "opcache not reset";} unlink(__FILE__);';
        $fileSystem->dumpFile($workingWebDirectory . '/opcache_reset.php', $opcacheReset);

        // Build Css Files
        $buildSassJob = new BuildSass();
        $buildSassJob->run($input, $output);

        // Build PackageStates.php
        $output->writeln('➤ Build PackageStates.php');
        $processString = Config::getPaths()->getWorkingBinDirectory() . '/typo3cms install:generatepackagestates';
        $process = new Process($processString, $buildDirectory);
        $process->mustRun();
        $output->writeln("\r\033[K\033[1A\r<info>✔</info>");

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
