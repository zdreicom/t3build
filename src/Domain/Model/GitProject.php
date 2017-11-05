<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

class GitProject
{
    /**
     * @var string
     */
    protected $user = 'git';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $fullName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $number = '';

    /**
     * @param string $uri
     */
    public function parse(string $uri)
    {
        $protocol = 'ssh';

        if (strpos($uri, 'https://') === 0) {
            $uri = substr($uri, 8);
            $protocol = 'http';
        }

        if (strpos($uri, 'http://') === 0) {
            $uri = substr($uri, 7);
            $protocol = 'http';
        }

        $parts = explode('@', $uri);

        if (count($parts) === 1) {
            $user = '';
            $uri = $parts[0];
        } else {
            $user = $parts[0];
            $uri = $parts[1];
        }

        if ($protocol === 'ssh') {
            $parts = explode(':', $uri);
            $host = $parts[0];
            $path = explode('.', $parts[1])[0];
        } else {
            $parts = explode('/', $uri, 2);
            $host = $parts[0];
            $path = explode('.', $parts[1])[0];
        }

        $this->path = $path;
        $this->user = $user;
        $this->host = $host;

        $parts = explode('/', $this->path);
        $this->fullName = $parts[count($parts) - 1];
        $parts = explode('-', $this->fullName, 2);

        $this->number = $parts[0];
        $this->name = $parts[1];

        // git@git.z3.ag:vslf/p1125-vslf.git
        // https://git.z3.ag/vslf/p1125-vslf.git
        // https://gitlab-ci-token:xxxxxxxxxxxxxxxxxxxx@git.z3.ag/z3/dbd/p1136-t3build.git
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }
}
