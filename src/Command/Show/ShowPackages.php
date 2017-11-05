<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Show;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Z3\T3build\Command\AbstractCommand;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Domain\Repository\PackageRepository;

class ShowPackages extends AbstractCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('show:packages')
            // the short description shown while running "php bin/console list"
            ->setDescription('Shows all packages t3build will work on');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var Package[] $packages */
        $packages = PackageRepository::getPackages();

        $ioTitle = new SymfonyStyle($input, $output);
        $ioTitle->title($this->getDescription());

        $tableContent = [];
        foreach ($packages as $package) {
            $tableContent[] = [$package->getName(), $package->getType()];
        }

        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['Name', 'Type'],
            $tableContent
        );
    }
}
