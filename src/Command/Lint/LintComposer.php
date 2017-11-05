<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Lint;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Command\AbstractPackageCommand;
use Z3\T3build\Domain\Model\Package;

class LintComposer extends AbstractPackageCommand
{
    protected function configure()
    {
        $this->task = 'lint';
        $this->type = 'composer';

        $this->needInputPath = true;

        $this
            // the name of the command (the part after "bin/console")
            ->setName($this->task . ':' . $this->type)
            // the short description shown while running "php bin/console list"
            ->setDescription('Lints the composer.json files');
    }

    /**
     * @param Package $package
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function executePackage(Package $package, InputInterface $input, OutputInterface $output)
    {
        $processString = 'composer validate -n --no-check-lock';
        $process = new Process($processString, $this->inputPath);
        $process->mustRun();
    }
}
