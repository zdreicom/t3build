<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Domain\Model\SshKey;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;
use Z3\T3build\Service\Configuration\Configuration;
use Z3\T3build\Service\Git\GitLabService;

class MakeAndSetKey extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'config';
    }

    public function getJobTask(): string
    {
        return 'makeAndSetKey';
    }

    public function getJobType(): string
    {
        return '';
    }

    public function isMultiJob(): bool
    {
        return false;
    }

    public function isNodeJob(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Make and upload host keys';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @throws \Exception
     * @return void
     */
    public function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $environment = $arguments['environment'];
        $projectConfigurationDirectory = Config::getPaths()->getProjectRootDirectory() . '/configuration';

        if (!@mkdir($projectConfigurationDirectory) && !is_dir($projectConfigurationDirectory)) {
            throw new \Exception('Can not cread configuration folder');
        }
        $localConfigurationFile = $projectConfigurationDirectory . '/Deploy.yaml';

        $localConfiguration = new Configuration();

        if (is_file($localConfigurationFile)) {
            $localConfiguration->addConfiguration(Yaml::parse(file_get_contents($localConfigurationFile)));
        }

        $git = new GitLabService();
        $gitProject = $git->getGitProject();
        $keyName = $gitProject->getNumber() . '_' . $environment;

        $key = new SshKey();
        $key->makeNewKey($keyName);

        $localConfiguration->setConfigurationString('deploy/' . $environment . '/key/host', 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAlxe0IL/kah6Niulw1IVzxaLySJ9pjzzAR7/MUkpUZLAgE/dK3ct1Q1ZHErqpluHJi4eZiOD15tAsu4qVBIlAAhzRetUOjYyqFDpCGPfATrBKNu5mXV9H0gbRATgn57y3jODZi/8Fhg8ElpVvK3xZCTxXm2PzfjuEa9RpnNhN033P6sjfAJ5mUfMJZmPjj/ed9Mf6PlNR+Kx1MSw0NsVN5n8kffKao6x9ZbfKne8hoKn8eaJ1qzgXE2AiMYN0xvsBY4ANE92ZLA0n8Sy9xg8j/e12yGdibyy+UFgxXZkWjn5fz3mfJUwBgMJZQAwR5a2EOEyzlyNyP/TBd6wATqL/UQ==');
        $localConfiguration->setConfigurationString('deploy/' . $environment . '/key/public', $key->getPublic());

        file_put_contents($localConfigurationFile, $localConfiguration->exportConfigurationYaml());

        $this->writeKeyToAuthorizedKeys($environment, $key);
        $this->writePrivateKeyToGitLab('KEY_' . strtoupper($environment), $key->getPrivate(), $environment);
        $this->writePrivateKeyToGitLab('PASSPHRASE_' . strtoupper($environment), $key->getPassphrase(), $environment);
    }

    /**
     * @param string $environment
     * @param SshKey $key
     */
    private function writeKeyToAuthorizedKeys(string $environment, SshKey $key)
    {
        $remoteCommand = 'echo "' . $key->getPublic() . '" >> ~/.ssh/authorized_keys';
        $this->executeRemoteCommand($environment, $remoteCommand);
    }

    private function writePrivateKeyToGitLab(string $key, string $value, string $environment)
    {
        $git = new GitLabService();
        $git->writeVariable($key, $value, true, $environment);
    }

    /**
     * @param string $environment
     * @param string $remoteCommand
     * @param string $path
     */
    protected function executeRemoteCommand(string $environment, string $remoteCommand, string $path = '')
    {
        $host = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/host', '');
        if (strlen($path) === 0) {
            $path = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/path', '');
        }
        $sshUser = Config::getProjectConfiguration()->getConfigurationString('deploy/' . $environment . '/user', '');
        $remoteCommand = 'cd ' . $path . ' && ' . $remoteCommand;
        $command = 'ssh ' . $sshUser . '@' . $host . ' ' . escapeshellarg($remoteCommand);
        $process = new Process($command);
        $process->mustRun();
    }
}
