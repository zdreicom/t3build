<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Time;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Git\GitLabService;

class TimeCorrection extends AbstractJob
{
    public function getJobClass(): string
    {
        return 'helper';
    }

    public function getJobTask(): string
    {
        return 'commit';
    }

    public function getJobType(): string
    {
        return 'git';
    }

    public function isMultiJob(): bool
    {
        return false;
    }

    public function isNodeJob(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Commit to GIT';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $arguments
     */
    public function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = [])
    {
        $git = new GitLabService();
        $issueNumber = $git->guessIssueNumber();
        $issueName = $git->getIssueTitle($issueNumber);

        $io = new SymfonyStyle($input, $output);

        $issueNumber = $io->ask('Issue: ' . $issueName, $issueNumber, function ($issueNumber) {
            return $this->validatIssue($issueNumber);
        });
        $message = $io->ask('Message', '');

        $minutes = $io->ask('Minutes', 0, function ($minutes) {
            return $this->validatMinutes($minutes);
        });

        $issueName = $git->getIssueTitle((int) $issueNumber);

        $tableContent = [
            ['Type', 'TIME'],
            ['Issue', $issueName],
            ['Minutes', $minutes],
            ['Message', $message]
        ];

        $io->table(
            ['Key', 'value'],
            $tableContent
        );

        $yesNo = $io->choice('You like to commit', ['yes' => 'y', 'no' => 'n'], 'n');

        if ($yesNo === 'no') {
            return;
        }

        $filesystem = new Filesystem();

        $filesystem->appendToFile('time_correction.txt', '[TIME] ' . $minutes . "\t" . $message . "\n");
        $git->addFile('time_correction.txt');
        $git->addCommitPush('TIME', (int) $issueNumber, $message, (int) $minutes);
    }

    private function validatIssue($issueNumber)
    {
        $issueNumber = (int) $issueNumber;
        if (is_int($issueNumber) === false || $issueNumber <= 0) {
            throw new \RuntimeException('This issue number is not valid.');
        }

        return $issueNumber;
    }

    private function validatMinutes($minutes)
    {
        $minutes = (int) $minutes;
        if (is_int($minutes) === false) {
            throw new \RuntimeException('The minutes must be an number');
        }

        if ($minutes === 0) {
            throw new \RuntimeException('The minutes must not be null');
        }
        return $minutes;
    }
}
