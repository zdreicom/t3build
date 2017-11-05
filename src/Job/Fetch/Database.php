<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Fetch;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;

class Database extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'fetch';
    }

    public function getJobTask(): string
    {
        return 'database';
    }

    public function getJobType(): string
    {
        return 'fetch';
    }

    public function getDescription(): string
    {
        return 'Fetchs the database';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $arguments['environment'];

        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        $user = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/user', '');
        $projectPath = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/path', '');

        $fileName = Config::getProject()->getFullName() . '-' . $environment . '.sql.gz';
        $artifacts = Config::getPaths()->getProjectArtifactDirectory();
        $dumpFile = $artifacts . '/' . $fileName;

        $command = 'ssh ' . $user . '@' . $host . ' \'cd ' . $projectPath . '/current && php_cli bin/typo3cms database:export | gzip\'  > ' . $dumpFile;
        $process = new Process($command, null, null, null, 3600);
        $process->mustRun();
    }
}
