<?php
declare(strict_types=1);

namespace Z3\T3build\Service\Configuration;

class UserConfiguration extends Configuration
{
    /**
     *
     */
    public function save()
    {
        $configurationFile = $_SERVER['HOME'] . '/.t3build';
        file_put_contents($configurationFile, $this->exportConfigurationYaml());
    }
}
