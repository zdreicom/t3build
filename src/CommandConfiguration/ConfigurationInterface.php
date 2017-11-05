<?php
declare(strict_types=1);

namespace Z3\T3build\CommandConfiguration;

interface ConfigurationInterface
{

    /**
     * @return string
     */
    public function getConfigurationString() : string;

    /**
     * @return string
     */
    public function getConfigurationFile() : string;
}
