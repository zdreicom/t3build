<?php
declare(strict_types=1);

namespace Z3\T3build\Job\Git;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Z3\T3build\Job\AbstractJob;
use Z3\T3build\Service\Config;
use Z3\T3build\Service\Git\GitLabService;

class Commit extends AbstractJob
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
     * @return int|null
     */
    public function runSingleJob(InputInterface $input, OutputInterface $output, array $arguments = []): ?int
    {
        $git = new GitLabService();
        $issueNumber = $git->guessIssueNumber();
        $issueName = $git->getIssueTitle($issueNumber);

        $io = new SymfonyStyle($input, $output);
        $type = $io->ask('Type TASK(t), FEATURE (f), BUGFIX (b) or CLEANUP(c): ', 'TASK', function ($type) {
            if ($type === 't') {
                $type = 'TASK';
            }
            if ($type === 'f') {
                $type = 'FEATURE';
            }
            if ($type === 'b') {
                $type = 'BUGFIX';
            }
            if ($type === 'c') {
                $type = 'CLEANUP';
            }
            if ($type !== 'TASK' && $type !== 'FEATURE' && $type !== 'BUGFIX' && $type !== 'CLEANUP') {
                throw new \RuntimeException('This type must either be TASK(t), FEATURE (f), BUGFIX (b) or CLEANUP(c)');
            }
            return $type;
        });

        $issueNumber = $io->ask('Issue: ' . $issueName, $issueNumber, function ($issueNumber) {
            return $this->validatIssue($issueNumber);
        });
        $message = $io->ask('Message', '');

        $speendTime = $this->getSpeendTime();

        $minutes = $io->ask('Minutes', $speendTime, function ($minutes) {
            return $this->validatMinutes($minutes);
        });

        $issueName = $git->getIssueTitle((int) $issueNumber);

        $tableContent = [
            ['Type', $type],
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
            return null;
        }

        $git->addCommitPush($type, (int) $issueNumber, $message, (int) $minutes);
        $this->setSpeendTime(time());

        return null;
    }

    /**
     * @return int
     */
    private function getSpeendTime(): int
    {
        $lastCommit = Config::getUserConfiguration()->getConfigurationInt('git/lastcommit', 0);
        $timeSpan = time() - $lastCommit;
        return (int) ($timeSpan / 60);
    }

    /**
     * @param int $time
     */
    private function setSpeendTime(int $time)
    {
        Config::getUserConfiguration()->setConfigurationInt('git/lastcommit', $time);
        Config::getUserConfiguration()->save();
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
        if (is_int($minutes) === false || $minutes < 0) {
            throw new \RuntimeException('The minutes must be an number');
        }

        return $minutes;
    }
}
