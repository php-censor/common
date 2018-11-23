<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Project\ProjectInterface;

class VariableInterpolator implements VariableInterpolatorInterface
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * @param BuildInterface   $build
     * @param ProjectInterface $project
     * @param string           $url
     */
    public function __construct(
        BuildInterface $build,
        ProjectInterface $project,
        string $url
    ) {
        $this->initVariables($build, $project, $url);
        $this->initEnvironmentVariables();
    }

    /**
     * {@inheritdoc}
     */
    public function interpolate(string $string): string
    {
        return \str_replace(
            \array_keys($this->variables),
            \array_values($this->variables),
            $string
        );
    }

    /**
     * @param BuildInterface   $build
     * @param ProjectInterface $project
     * @param string           $url
     */
    private function initVariables(
        BuildInterface $build,
        ProjectInterface $project,
        string $url
    ) {
        $this->variables = [
            '%COMMIT_ID%'       => $build->getCommitId(),
            '%SHORT_COMMIT_ID%' => \substr($build->getCommitId(), 0, 7),
            '%COMMITTER_EMAIL%' => $build->getCommitterEmail(),
            '%COMMIT_MESSAGE%'  => $build->getCommitMessage(),
            '%COMMIT_LINK%'     => $build->getCommitLink(),
            '%PROJECT_ID%'      => $project->getId(),
            '%PROJECT_TITLE%'   => $project->getTitle(),
            '%PROJECT_LINK%'    => \rtrim($url, '/') . '/project/view/' . $project->getId(),
            '%BUILD_ID%'        => $build->getId(),
            '%BUILD_PATH%'      => $build->getBuildPath(),
            '%BUILD_LINK%'      => \rtrim($url, '/') . '/build/view/' . $build->getId(),
            '%BRANCH%'          => $build->getBranch(),
            '%BRANCH_LINK%'     => $build->getBranchLink(),
            '%ENVIRONMENT%'     => $build->getEnvironment(),
        ];
    }

    private function initEnvironmentVariables()
    {
        \putenv('PHP_CENSOR=1');
        \putenv('PHP_CENSOR_COMMIT_ID=' . $this->variables['%COMMIT_ID%']);
        \putenv('PHP_CENSOR_SHORT_COMMIT_ID=' . $this->variables['%SHORT_COMMIT_ID%']);
        \putenv('PHP_CENSOR_COMMITTER_EMAIL=' . $this->variables['%COMMITTER_EMAIL%']);
        \putenv('PHP_CENSOR_COMMIT_MESSAGE=' . $this->variables['%COMMIT_MESSAGE%']);
        \putenv('PHP_CENSOR_COMMIT_LINK=' . $this->variables['%COMMIT_LINK%']);
        \putenv('PHP_CENSOR_PROJECT_ID=' . $this->variables['%PROJECT_ID%']);
        \putenv('PHP_CENSOR_PROJECT_TITLE=' . $this->variables['%PROJECT_TITLE%']);
        \putenv('PHP_CENSOR_PROJECT_LINK=' . $this->variables['%PROJECT_LINK%']);
        \putenv('PHP_CENSOR_BUILD_ID=' . $this->variables['%BUILD_ID%']);
        \putenv('PHP_CENSOR_BUILD_PATH=' . $this->variables['%BUILD_PATH%']);
        \putenv('PHP_CENSOR_BUILD_LINK=' . $this->variables['%BUILD_LINK%']);
        \putenv('PHP_CENSOR_BRANCH=' . $this->variables['%BRANCH%']);
        \putenv('PHP_CENSOR_BRANCH_LINK=' . $this->variables['%BRANCH_LINK%']);
        \putenv('PHP_CENSOR_ENVIRONMENT=' . $this->variables['%ENVIRONMENT%']);
    }
}
