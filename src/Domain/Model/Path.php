<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model;

class Path
{
    /**
     * @var string
     */
    protected $inputPath = '';

    /**
     * @var string
     */
    protected $outputPath = '';

    /**
     * @var string
     */
    protected $configFile = '';

    public function __construct(string $inputPath, string $outputPath, string $configFile)
    {
        $this->inputPath = $inputPath;
        $this->outputPath = $outputPath;
        $this->configFile = $configFile;
    }

    /**
     * @return string
     */
    public function getInputPath(): string
    {
        return $this->inputPath;
    }

    /**
     * @param string $inputPath
     */
    public function setInputPath(string $inputPath)
    {
        $this->inputPath = $inputPath;
    }

    /**
     * @return string
     */
    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    /**
     * @param string $outputPath
     */
    public function setOutputPath(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    /**
     * @return string
     */
    public function getConfigFile(): string
    {
        return $this->configFile;
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile(string $configFile)
    {
        $this->configFile = $configFile;
    }

    public function getHash()
    {
        return md5($this->inputPath . $this->outputPath);
    }
}
