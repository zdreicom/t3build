<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Version;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class Set extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'version';
    }

    public function getJobTask(): string
    {
        return 'set';
    }

    public function getJobType(): string
    {
        return 'set';
    }

    public function getDescription(): string
    {
        return 'Set the system version';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @return int|null
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = []): ?int
    {
        $versionName =  $input->getArgument('versionName');
        $workingWebDirectory = Config::getPaths()->getWorkingWebDirectory();

        $versionDirectory = $workingWebDirectory . '/Version/' . $versionName;
        $composerFile =  $versionDirectory . '/composer.json';
        $composerLockFile =  $versionDirectory . '/composer.lock';

        $fileSystem = new Filesystem();
        if ($fileSystem->exists($versionDirectory) === false) {
            $output->writeln('<error>Version does not exist</error>');
            return 1;
        }
        if ($fileSystem->exists($composerFile) === false) {
            $output->writeln('<error>composer.json does not exist</error>');
            return 1;
        }
        if ($fileSystem->exists($composerLockFile) === false) {
            $output->writeln('<error>composer.lock does not exist</error>');
            return 1;
        }

        $fileSystem->copy($composerFile, $workingWebDirectory . '/composer.json', true);
        $fileSystem->copy($composerLockFile, $workingWebDirectory . '/composer.lock', true);

        return null;
    }
}
