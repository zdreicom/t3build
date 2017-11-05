<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Deploy;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Service\Config;

class SetTemporarySSHKey extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('deploy:setsshkey')
            // the short description shown while running "php bin/console list"
            ->setDescription('Set the ssh ci key for deployment');

        $this->addArgument('environment', InputArgument::OPTIONAL, 'The environment which database  should be fetched (production,staging)', 'staging');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment');
        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        $keyHost = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/key/host', '');
        $keyPublic = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/key/public', '');
        $keyPrivate = getenv('KEY_' . strtoupper($environment));
        $passphrase = getenv('PASSPHRASE_' . strtoupper($environment));

        $ssh = $_SERVER['HOME'] . '/.ssh';
        @mkdir($ssh);

        if (!file_exists($ssh . '/known_hosts')) {
            $process = new Process('echo "' . $host . ' ' . $keyHost . '" > ' . $ssh . '/known_hosts');
            $process->mustRun();
        }

        if (!file_exists($ssh . '/id_rsa.pub')) {
            $process = new Process('echo "' . $keyPublic . '" > ' . $ssh . '/id_rsa.pub');
            $process->mustRun();
        }

        if ($keyPrivate === false) {
            throw new \Exception('No private key set');
        }

        if (!file_exists($ssh . '/id_rsa')) {
            $process = new Process('echo "' . $keyPrivate . '" > ' . $ssh . '/id_rsa && chmod 600 ' . $ssh . '/id_rsa');
            $process->mustRun();
        }

        if ($passphrase !== false) {
            $process = new Process('ssh-keygen -p -P ' . $passphrase . ' -N ""  -f ' . $ssh . '/id_rsa');
            $process->mustRun();
        }
    }
}
