<?php
declare(strict_types=1);

namespace Z3\T3build\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Z3\T3build\CommandConfiguration\ConfigurationFactory;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Model\Path;
use Z3\T3build\Domain\Repository\PackageRepository;

abstract class AbstractPackageCommand extends AbstractCommand
{

    /**
     * @var Path
     */
    protected $path;

    /**
     * @var string
     */
    protected $inputPath = '';

    /**
     * @var string
     */
    protected $outputPath = '';

    /**
     * @var string
     */
    protected $configFile = '';

    /**
     * @var \Z3\T3build\Domain\Model\ConfigurationModel
     */
    protected $configuration;

    /**
     * @var bool
     */
    protected $needInputPath = false;

    /**
     * @var bool
     */
    protected $needOutputPath = false;

    /**
     * @var bool
     */
    protected $needConfigFile = false;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'WARNING: Command is deprecated please use an Job instead.' . "\n";

        /** @var Package[] $packages */
        $packages = PackageRepository::getPackages();

        $ioTitle = new SymfonyStyle($input, $output);
        $ioTitle->title($this->getDescription());

        foreach ($packages as $package) {
            $path = $package->getPath($this->type);
            foreach ($path->getInput() as $inputPath) {
                $this->path = new Path($inputPath, $path->getOutput(), '');

                $output->writeln('➤ Executing task on <info>' . $package->getName() . '</info>');

                if ($this->needConfigFile) {
                    $configuration = ConfigurationFactory::getCommandConfiguration($package, $this->path, $this->task, $this->type);
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

                $this->executePackage($package, $input, $output);
                $output->writeln("\r\033[K\033[1A\r<info>✔</info>");
            }
        }
    }

    /**
     * @param Package $package
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function executePackage(Package $package, InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * @return string
     */
    private function canTaskBeExecuteOnPackage() : string
    {
        if ($this->needInputPath && !is_dir($this->path->getInputPath())) {
            return 'no source paths found';
        }
        if ($this->needOutputPath && !is_dir($this->path->getOutputPath())) {
            return 'no target path found';
        }
        if ($this->needConfigFile && !is_file($this->path->getConfigFile())) {
            return 'no config found';
        }
        return '';
    }
}
