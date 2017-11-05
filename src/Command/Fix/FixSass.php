<?php
declare(strict_types=1);

namespace Z3\T3build\Command\Fix;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Z3\T3build\Command\AbstractPackageCommandNode;
use Z3\T3build\Domain\Model\Package;
use Z3\T3build\Service\Path\BasePaths;
use Z3\T3build\Utility\FileDirectoryUtility;
use Z3\T3buildNode\Service\Path\NodePaths;

class FixSass extends AbstractPackageCommandNode
{
    protected function configure()
    {
        $this->task = 'fix';
        $this->type = 'sass';

        $this->needInputPath = true;
        $this->needConfigFile= true;

        $this
            // the name of the command (the part after "bin/console")
            ->setName($this->task . ':' . $this->type)
            // the short description shown while running "php bin/console list"
            ->setDescription('Fixes the Sass files');
    }

    /**
     * @param Package $package
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function executePackage(Package $package, InputInterface $input, OutputInterface $output)
    {
        $ignoreFolders = [];
        if (array_key_exists('ignoreFolders', $this->configuration->getConfigurationArray())) {
            $ignoreFolders = $this->configuration->getConfigurationArray()['ignoreFolders'];
        }

        $files = FileDirectoryUtility::getFilesInDir($this->inputPath, 'scss', true, 0, 100);
        foreach ($files as $file) {
            $filePath = substr($file, strlen($this->inputPath) + 1);
            $firstFolder = explode('/', $filePath)[0];

            if (!in_array($firstFolder, $ignoreFolders)) {
                $this->fixFile($file);
            }
        }
    }

    private function fixFile($inputFile)
    {
        $fixSass = NodePaths::getNodeExecutable() . ' ' . NodePaths::getNodeBinDirectory() . '/stylefmt';
        $outputFile = BasePaths::getT3buildTempDirectory() . '/scss-temp.scss';

        $processString  = '';
        $processString .= 'cat ' . $inputFile . ' | ';
        $processString .= $fixSass . ' --config ' . $this->configFile;
        $processString .= ' > ' . $outputFile;

        $process = new Process($processString);
        $process->mustRun();

        $moveFile = 'mv ' . $outputFile . ' ' . $inputFile;

        $process = new Process($moveFile);
        $process->mustRun();
    }
}
