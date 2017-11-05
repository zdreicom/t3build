<?php
declare(strict_types=1);

namespace Z3\T3build\Domain\Model\GitLab;

use Z3\T3build\Service\Configuration\Configuration;

class Job extends AbstractJob
{
    /**
     * @param string $name
     * @param Configuration $config
     * @return Job
     */
    public static function parse(string $name, Configuration $config) : Job
    {
        $job = new self();
        $job->name = $name;
        $job->stage = $config->getConfigurationString($name . '/stage');

        return $job;
    }
}
