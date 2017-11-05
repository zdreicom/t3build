<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model\GitLab;

abstract class AbstractJob
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $stage = '';

    /**
     * @var string
     */
    protected $image = 'php-7.0';

    /**
     * @var string[]
     */
    protected $only = [];

    /**
     * @var string[]
     */
    protected $beforeScript = [];

    /**
     * @var string[]
     */
    protected $script = [];

    /**
     * @var string[]
     */
    protected $artifactsPaths = [];

    /**
     * @var string
     */
    protected $artifactsexpireIn = '1 day';

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
    public function getStage(): string
    {
        return $this->stage;
    }

    /**
     * @param string $stage
     */
    public function setStage(string $stage)
    {
        $this->stage = $stage;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
    }

    /**
     * @return \string[]
     */
    public function getOnly(): array
    {
        return $this->only;
    }

    /**
     * @param \string[] $only
     */
    public function setOnly(array $only)
    {
        $this->only = $only;
    }

    /**
     * @return \string[]
     */
    public function getBeforeScript(): array
    {
        return $this->beforeScript;
    }

    /**
     * @param \string[] $beforeScript
     */
    public function setBeforeScript(array $beforeScript)
    {
        $this->beforeScript = $beforeScript;
    }

    /**
     * @return \string[]
     */
    public function getScript(): array
    {
        return $this->script;
    }

    /**
     * @param \string[] $script
     */
    public function setScript(array $script)
    {
        $this->script = $script;
    }

    /**
     * @return \string[]
     */
    public function getArtifactsPaths(): array
    {
        return $this->artifactsPaths;
    }

    /**
     * @param \string[] $artifactsPaths
     */
    public function setArtifactsPaths(array $artifactsPaths)
    {
        $this->artifactsPaths = $artifactsPaths;
    }

    /**
     * @return string
     */
    public function getArtifactsexpireIn(): string
    {
        return $this->artifactsexpireIn;
    }

    /**
     * @param string $artifactsexpireIn
     */
    public function setArtifactsexpireIn(string $artifactsexpireIn)
    {
        $this->artifactsexpireIn = $artifactsexpireIn;
    }
}
