<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

use Symfony\Component\Process\Process;
use Z3\T3build\Service\Config;

class SshKey
{

    /**
     * @var string
     */
    protected $private = '';

    /**
     * @var string
     */
    protected $public = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $passphrase = '';

    public function makeNewKey($name)
    {
        $this->name = $name;
        $keyFile = Config::getPaths()->getProjectTemporaryDirectory('key') . '/id_rsa';
        $this->passphrase = bin2hex(random_bytes(16));
        $command = 'ssh-keygen -t rsa -b 4096 -N ' . $this->passphrase . ' -C ' . $name . ' -f ' . $keyFile;
        $process = new Process($command);
        $process->mustRun();

        $this->private = file_get_contents($keyFile);
        $this->public = file_get_contents($keyFile . '.pub');

        unlink($keyFile);
        unlink($keyFile . '.pub');
    }

    /**
     * @return string
     */
    public function getPrivate(): string
    {
        return $this->private;
    }

    /**
     * @return string
     */
    public function getPublic(): string
    {
        return $this->public;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassphrase(): string
    {
        return $this->passphrase;
    }
}
