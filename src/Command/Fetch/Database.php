<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Fetch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Fetch\Database as DatabaseJob;

class Database extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('database')
            // the short description shown while running "php bin/console list"
            ->setDescription('Fetch the database');

        $this->addArgument('environment', InputArgument::OPTIONAL, 'The environment which database  should be fetched (production,staging)', 'staging');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $job = new DatabaseJob();
        $job->run($input, $output, ['environment' => $environment]);
    }
}
