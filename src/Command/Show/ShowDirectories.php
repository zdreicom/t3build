<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Show;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Z3\T3build\Command\AbstractCommand;
use Z3\T3build\Service\Config;

class ShowDirectories extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('show:dir')
            // the short description shown while running "php bin/console list"
            ->setDescription('Shows the directories the task will work on');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ioTitle = new SymfonyStyle($input, $output);
        $ioTitle->title($this->getDescription());

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['Type', 'Path'],
            [
                ['Project root', Config::getPaths()->getProjectRootDirectory()],
                ['Project bin', Config::getPaths()->getProjectBinDirectory()],
                ['Project vendor', Config::getPaths()->getProjectVendorDirectory()],
                ['Project packages', Config::getPaths()->getProjectPackagesDirectory()]
            ]
        );

        $io->table(
            ['Type', 'Path'],
            [
                ['Working root', Config::getPaths()->getWorkingRootDirectory()],
                ['Working bin', Config::getPaths()->getWorkingBinDirectory()],
                ['Working vendor', Config::getPaths()->getWorkingVendorDirectory()],
                ['Working packages', Config::getPaths()->getWorkingPackagesDirectory()]
            ]
        );

        $io->table(
            ['Type', 'Path'],
            [
                ['t3build root', Config::getPaths()->getT3buildRootDirectory()],
                ['t3build bin', Config::getPaths()->getT3buildBinDirectory()],
                ['t3build vendor', Config::getPaths()->getT3buildVendorDirectory()],
                ['t3build soure', Config::getPaths()->getT3BuildSourceDirectory()]
            ]
        );
    }
}
