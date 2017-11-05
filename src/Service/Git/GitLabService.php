<?php
declare(strict_types=1);

namespace Z3\T3build\Service\Git;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Z3\T3build\Domain\Model\GitProject;
use Z3\T3build\Service\Config;
use Z3\T3build\Utility\GitLabUtility;

class GitLabService
{

    /**
     * @var \GitWrapper\GitWorkingCopy
     */
    public $git;

    public function __construct()
    {
        $this->git = new GitWorkingCopy(new GitWrapper(), Config::getPaths()->getWorkingRootDirectory());
    }

    /**
     * @return string
     */
    public function getCurrentWorkingBranch(): string
    {
        return $this->git->getBranches()->head();
    }

    /**
     * @return int
     */
    public function guessIssueNumber(): int
    {
        $branch = $this->getCurrentWorkingBranch();
        $issue = (int) explode('-', $branch)[0];
        return $issue;
    }

    /**
     * @param int $issueNumber
     * @return string
     */
    public function getIssueTitle(int $issueNumber): string
    {
        $gitProject = $this->getGitProject();
        if ($issueNumber <= 0) {
            return '';
        }
        return GitLabUtility::getIssueTitle(GitLabUtility::getGitLabToken($gitProject->getHost()), $gitProject->getPath(), $issueNumber);
    }

    /**
     * @return GitProject
     */
    public function getGitProject(): GitProject
    {
        $remote = $this->git->getRemotes();
        $out = new GitProject();
        $out->parse($remote['origin']['push']);
        return $out;
    }

    /**
     * @param string $type
     * @param int $issueNumber
     * @param string $message
     * @param int $minutes
     */
    public function addCommitPush(string $type, int $issueNumber, string $message, int $minutes)
    {
        $commitMessage = '[' . $type . '] #' . $issueNumber . ' ' . $message;
        if ($minutes > 0) {
            $commitMessage .= ' /s' . $minutes;
        }

        $this->git
            ->commit([
                'm' => $commitMessage
            ])
            ->push();
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $protected
     * @param string $environment
     */
    public function writeVariable(string $key, string $value, bool $protected, string $environment)
    {
        $gitProject = $this->getGitProject();
        return GitLabUtility::writeVariable(GitLabUtility::getGitLabToken($gitProject->getHost()), $gitProject->getPath(), $key, $value, $protected, $environment);
    }
}
