<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Fetch;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class Files extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'fetch';
    }

    public function getJobTask(): string
    {
        return 'files';
    }

    public function getJobType(): string
    {
        return 'fetch';
    }

    public function getDescription(): string
    {
        return 'Fetch the files from shared folder\'';
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
        $environment = $arguments['environment'];

        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        $user = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/user', '');
        $projectPath = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/path', '');

        $fileName = Config::getProject()->getFullName() . '-' . $environment . '.tar.gz';
        $artifacts = Config::getPaths()->getProjectArtifactDirectory();
        $dumpFile = $artifacts . '/' . $fileName;

        $command = 'ssh ' . $user . '@' . $host . ' \'cd ' . $projectPath . ' && tar cf -  shared | gzip\'  > ' . $dumpFile;
        $process = new Process($command, null, null, null, 3600);
        $process->mustRun();

        return null;
    }
}
