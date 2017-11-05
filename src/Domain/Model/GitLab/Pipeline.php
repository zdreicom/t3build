<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model\GitLab;

use Symfony\Component\Yaml\Yaml;
use Z3\T3build\Service\Configuration\Configuration;

class Pipeline
{

    /**
     * @var array
     */
    private static $keyWords = [
        'cache',
        'variables',
        'stages'
    ];

    /**
     * @var string[]
     */
    protected $variables = [];

    /**
     * @var string[]
     */
    protected $cachePaths = [];

    /**
     * @var string
     */
    protected $cacheKey = '';

    /**
     * @var array
     */
    protected $stagesOrder = [];

    /**
     * \Z3\T3build\Domain\Model\GitLab[];
     */
    protected $stages = [];

    /**
     * @param string $configString
     * @return Pipeline
     */
    public static function parse(string $configString) : Pipeline
    {
        $configArray = Yaml::parse($configString);
        var_dump($configArray);
        $config = new Configuration($configArray);
        $pipeline = new self();

        $pipeline->variables  = $config->getConfigurationArrayOfString('variables', []);
        $pipeline->cacheKey    = $config->getConfigurationString('cache/key', '');
        $pipeline->cachePaths = $config->getConfigurationArrayOfString('cache/paths', []);
        $pipeline->stagesOrder = $config->getConfigurationArrayOfString('stages', []);

        foreach ($configArray as $name => $value) {
            if (!in_array($name, self::$keyWords)) {
                $job = Job::parse($name, $config);
                echo $job->getName() . ' ' . $job->getStage() . "\n";
            }
        }

        return $pipeline;
    }

    /**
     * @return \string[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @return \string[]
     */
    public function getCachePaths(): array
    {
        return $this->cachePaths;
    }

    /**
     * @param string $value
     * @return Pipeline
     */
    public function addCachePath(string $value): Pipeline
    {
        if (!in_array($this->cachePaths, $value)) {
            $this->cachePaths[] = $this->cachePaths;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Pipeline
     */
    public function addVariable(string $key, string $value): Pipeline
    {
        $this->variable[$key] = $value;
        return $this;
    }
}
