<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Deploy;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Z3\T3build\Job\Config\Host;
use Z3\T3build\Service\Configuration\Configuration;

class SetHost extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('deploy:sethost')
            // the short description shown while running "php bin/console list"
            ->setDescription('Set the host configuration');

        $this->addArgument('environment', InputArgument::REQUIRED, 'The environment which should be configured (production,staging)');
        $this->addArgument('account', InputArgument::REQUIRED, 'The Mittwald account (p123456)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @throws \Exception
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $input->getArgument('environment');
        $account = $input->getArgument('account');
        $job = new Host();
        $job->run($input, $output, [
            'environment' => $environment,
            'account' => $account
        ]);
    }
}
