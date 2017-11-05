<?php
declare(strict_types=1);

namespace Z3\T3build\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Z3\T3build\CommandConfiguration\ConfigurationFactory;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;
use Z3\T3build\Domain\Repository\PackageRepository;
use Z3\T3build\Service\Config;

abstract class AbstractJob implements JobInterface
{
    public function isMultiJob(): bool
    {
        return false;
    }

    public function isNodeJob(): bool
    {
        return false;
    }

    public function needInputPath(): bool
    {
        return false;
    }

    public function needOutputPath(): bool
    {
        return false;
    }

    public function needConfigFile(): bool
    {
        return false;
    }

    /**
     * @var Path
     */
    protected $path;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @return void
     */
    public function run(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        if ($this->isNodeJob()) {
            Config::getPaths()->initNodePaths();
        }
        if ($this->isMultiJob()) {
            $this->runMultiJob($input, $output, $arguments);
        } else {
            $this->runSingleJob($input, $output, $arguments);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    private function runMultiJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        /** @var Package[] $packages */
        $packages = PackageRepository::getPackages();

        $ioTitle = new SymfonyStyle($input, $output);
        $ioTitle->title($this->getDescription());

        foreach ($packages as $package) {
            $path = $package->getPath($this->getJobType());
            foreach ($path->getInput() as $inputPath) {
                $this->path = new Path($inputPath, $path->getOutput(), '');

                $output->writeln('➤ Executing task on <info>' . $package->getName() . '</info>');

                if ($this->needConfigFile()) {
                    $configuration = ConfigurationFactory::getCommandConfiguration($package, $this->path, $this->getJobTask(), $this->getJobType());
                    $this->configFile = $configuration->getConfigurationFile();
                    $this->configuration = $configuration->getConfiguration();
                }
                $canTaskBeExecuteOnPackage = $this->canTaskBeExecuteOnPackage();
                if (strlen($canTaskBeExecuteOnPackage) > 0) {
                    $output->writeln("\r\033[K\033[1A\r<fg=yellow>✘</fg=yellow> skipped task <info>" . $package->getName() . '</info> ' . $canTaskBeExecuteOnPackage);
                    continue;
                }

                $this->inputPath = $this->path->getInputPath();
                $this->outputPath= $this->path->getOutputPath();

                $this->runSingleJob($input, $output, $arguments);
                $output->writeln("\r\033[K\033[1A\r<info>✔</info>");
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    protected function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        throw new LogicException('You must override the runSingleJob() method in the concrete command class.');
    }

    /**
     * @param string $environment
     * @param string $remoteCommand
     * @param string $projectPath
     */
    protected function executeRemoteCommand(string $environment, string $remoteCommand, string $projectPath = '')
    {
        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        $user = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/user', '');
        if (strlen($projectPath) === 0) {
            $projectPath = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/path', '') . '/current';
        }
        $remoteCommand = 'cd ' . $projectPath . ' && ' . $remoteCommand;
        $command = 'ssh ' . $user . '@' . $host . ' ' . escapeshellarg($remoteCommand);
        $process = new Process($command);
        $process->mustRun();
    }

    /**
     * @return string
     */
    private function canTaskBeExecuteOnPackage() : string
    {
        if ($this->needInputPath() && !is_dir($this->path->getInputPath())) {
            return 'no source paths found';
        }
        if ($this->needOutputPath() && !is_dir($this->path->getOutputPath())) {
            return 'no target path found';
        }
        if ($this->needConfigFile() && !is_file($this->path->getConfigFile())) {
            return 'no config found';
        }
        return '';
    }
}
