<?php
declare(strict_types=1);

namespace Z3\T3build\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Service\Bootstrap;
use Z3\T3build\Service\Configuration\Configuration;

abstract class AbstractCommand extends Command
{
    /**
     * @var string
     */
    protected $task = '';

    /**
     * @var string
     */
    protected $type = '';

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->rootPackage = Bootstrap::getRootPackage();
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'WARNING: Command is deprecated please use an Job instead.' . "\n";
        throw new LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * @param string $environment
     * @param string $remoteCommand
     * @param string $path
     */
    protected function executeRemoteCommand(string $environment, string $remoteCommand, string $path = '')
    {
        $host = Configuration::getConfigurationString('deploy/' . $environment . '/host', '');
        if (strlen($path) === 0) {
            $path = Configuration::getConfigurationString('deploy/' . $environment . '/path', '');
        }
        $sshUser = Configuration::getConfigurationString('deploy/' . $environment . '/user', '');
        $sshKeyPublic = Configuration::getConfigurationString('deploy/' . $environment . '/key/public', '');
        $sshKeyHost = Configuration::getConfigurationString('deploy/' . $environment . '/key/host', '');

        $remoteCommand = 'cd ' . $path . ' && ' . $remoteCommand;

        $command = 'ssh ' . $sshUser . '@' . $host . ' ' . escapeshellarg($remoteCommand);
        $process = new Process($command);
        $process->mustRun();
    }
}
