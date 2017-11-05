<?php
declare(strict_types=1);

namespace Z3\T3build\Job;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface JobInterface
{
    /**
     * @return string
     */
    public function getJobClass(): string;

    /**
     * @return string
     */
    public function getJobTask(): string;

    /**
     * @return string
     */
    public function getJobType(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return bool
     */
    public function isMultiJob(): bool;

    /**
     * @return bool
     */
    public function isNodeJob(): bool;

    /**
     * @return bool
     */
    public function needInputPath(): bool;

    /**
     * @return bool
     */
    public function needOutputPath(): bool;

    /**
     * @return bool
     */
    public function needConfigFile(): bool;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     * @return mixed
     */
    public function run(InputInterface $input, OutputInterface $output, array $arguments = []);
}
