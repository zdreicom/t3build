<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Build;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Command\AbstractPackageCommandNode;
use Z3\T3build\Domain\Model\Package;
use Z3\T3buildNode\Service\Path\NodePaths;

class BuildTypeScript extends AbstractPackageCommandNode
{
    protected function configure()
    {
        $this->task = 'build';
        $this->type = 'typescript';

        $this->needInputPath = true;
        $this->needOutputPath = true;
        $this->needConfigFile = true;

        $this
            // the name of the command (the part after "bin/console")
            ->setName($this->task . ':' . $this->type)
            // the short description shown while running "php bin/console list"
            ->setDescription('Builds the Js files from the TypeScript files');
    }

    /**
     * @param Package $package
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function executePackage(Package $package, InputInterface $input, OutputInterface $output)
    {
        NodePaths::initNode();
        $typeScriptCompiler = NodePaths::getNodeExecutable() . ' ' . NodePaths::getNodeBinDirectory() . '/tsc';
        $processString = $typeScriptCompiler . ' -p ' . $this->configFile;
        $process = new Process($processString);
        $process->mustRun();
    }
}
