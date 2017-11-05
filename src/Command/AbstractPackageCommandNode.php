<?php
declare(strict_types=1);

namespace Z3\T3build\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3buildNode\Service\Path\NodePaths;

abstract class AbstractPackageCommandNode extends AbstractPackageCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        NodePaths::initNode();
        parent::execute($input, $output);
    }
}
