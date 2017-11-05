<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Ci;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOpcache extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('clear:opcache')
            // the short description shown while running "php bin/console list"
            ->setDescription('Clears the php Opcache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (function_exists('opcache_reset')) {
            \opcache_reset();
        } else {
            $output->writeln('WARNING: Can not reset opcache_reset');
        }
    }
}
