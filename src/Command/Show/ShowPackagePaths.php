<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Show;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Z3\T3build\Command\AbstractCommand;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Repository\PackageRepository;

class ShowPackagePaths extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('show:package:paths')
            // the short description shown while running "php bin/console list"
            ->setDescription('Shows the directories of the packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var Package[] $packages */
        $packages = PackageRepository::getPackages();

        $ioTitle = new SymfonyStyle($input, $output);
        $ioTitle->title($this->getDescription());

        foreach ($packages as $package) {
            $io = new SymfonyStyle($input, $output);
            $io->section($package->getName());

            $sassInput = '';
            foreach ($package->getPath('sass')->getInput() as $sassInputPath) {
                $sassInput .= ', ' . $sassInputPath;
            }
            $sassInput = ltrim($sassInput, ', ');
            $phpInput = '';
            foreach ($package->getPath('php')->getInput() as $phpInputPath) {
                $phpInput .= ', ' . $phpInputPath;
            }
            $phpInput = ltrim($phpInput, ', ');
            $composerInput = '';
            foreach ($package->getPath('composer')->getInput() as $composerInputPath) {
                $composerInput .= ', ' . $composerInputPath;
            }
            $composerInput = ltrim($composerInput, ', ');

            $tableContent = [];
            $tableContent[] = ['Sass', $sassInput];
            $tableContent[] = ['Sass Output', $package->getPath('sass')->getOutput()];
            $tableContent[] = ['PHP', $phpInput];
            $tableContent[] = ['Composer', $composerInput];

            $io->table(
                ['Type', 'Path'],
                $tableContent
            );
        }
    }
}
