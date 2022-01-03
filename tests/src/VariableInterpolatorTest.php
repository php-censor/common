<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Common;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Project\ProjectInterface;
use PHPCensor\Common\VariableInterpolator;
use PHPCensor\Common\VariableInterpolatorInterface;
use PHPUnit\Framework\TestCase;

class VariableInterpolatorBuild implements BuildInterface
{
    public function getId(): int
    {
        return 10;
    }

    public function getProjectId(): int
    {
        return 20;
    }

    public function getCommitId(): string
    {
        return 'commit_id';
    }

    public function getCommitterEmail(): string
    {
        return 'committer_email';
    }

    public function getCommitMessage(): string
    {
        return 'commit_message';
    }

    public function getCommitLink(): string
    {
        return 'commit_link';
    }

    public function getBranch(): string
    {
        return 'branch';
    }

    public function getBranchLink(): string
    {
        return 'branch_link';
    }

    public function getTag(): string
    {
        return 'tag';
    }

    public function getEnvironment(): string
    {
        return 'environment';
    }

    public function getSource(): int
    {
        return 30;
    }

    public function getUserId(): int
    {
        return 40;
    }

    public function getExtra(?string $key = null)
    {
        return null;
    }

    public function getStatus(): int
    {
        return 50;
    }

    public function getLog(): string
    {
        return "example log text 1\nexample log text 2";
    }

    public function getCreateDate(): ?\DateTime
    {
        return new \DateTime();
    }

    public function getStartDate(): ?\DateTime
    {
        return new \DateTime();
    }

    public function getFinishDate(): ?\DateTime
    {
        return new \DateTime();
    }

    public function getBuildPath(): string
    {
        return 'build_path';
    }

    public function isSuccessful(): bool
    {
        return true;
    }

    public function getBuildDirectory(): string
    {
        return 'build_directory';
    }

    public function getBuildBranchDirectory(): string
    {
        return 'build_branch_directory';
    }

    public function isDebug(): bool
    {
        return false;
    }

    public function getLink(): string
    {
        return 'http://example.com/build/view/' . $this->getId();
    }
}

class VariableInterpolatorProject implements ProjectInterface
{
    public function getId(): int
    {
        return 20;
    }

    public function getTitle(): string
    {
        return 'title';
    }

    public function getBuildConfig(): array
    {
        return [];
    }

    public function getLink(): string
    {
        return 'http://example.com/project/view/' . $this->getId();
    }
}

class VariableInterpolatorTest extends TestCase
{
    public function testConstruct(): void
    {
        $interpolator = new VariableInterpolator(
            new VariableInterpolatorBuild(),
            new VariableInterpolatorProject(),
            '1.0.0'
        );

        self::assertInstanceOf(VariableInterpolatorInterface::class, $interpolator);
        self::assertInstanceOf(VariableInterpolator::class, $interpolator);
    }

    public function testInterpolate(): void
    {
        $interpolator = new VariableInterpolator(
            new VariableInterpolatorBuild(),
            new VariableInterpolatorProject(),
            '1.0.0'
        );

        self::assertEquals(
            '
Text with
commit_id,
commit_,
committer_email,
commit_message,
commit_link,
20,
title,
http://example.com/project/view/20,
10,
build_path,
http://example.com/build/view/10,
branch,
branch_link,
environment,
1.0.0
for testing.',
            $interpolator->interpolate('
Text with
%COMMIT_ID%,
%SHORT_COMMIT_ID%,
%COMMITTER_EMAIL%,
%COMMIT_MESSAGE%,
%COMMIT_LINK%,
%PROJECT_ID%,
%PROJECT_TITLE%,
%PROJECT_LINK%,
%BUILD_ID%,
%BUILD_PATH%,
%BUILD_LINK%,
%BRANCH%,
%BRANCH_LINK%,
%ENVIRONMENT%,
%SYSTEM_VERSION%
for testing.')
        );

        self::assertEquals('1', \getenv('PHP_CENSOR'));
        self::assertEquals('commit_id', \getenv('PHP_CENSOR_COMMIT_ID'));
        self::assertEquals('commit_', \getenv('PHP_CENSOR_SHORT_COMMIT_ID'));
        self::assertEquals('committer_email', \getenv('PHP_CENSOR_COMMITTER_EMAIL'));
        self::assertEquals('commit_message', \getenv('PHP_CENSOR_COMMIT_MESSAGE'));
        self::assertEquals('commit_link', \getenv('PHP_CENSOR_COMMIT_LINK'));
        self::assertEquals(20, \getenv('PHP_CENSOR_PROJECT_ID'));
        self::assertEquals('title', \getenv('PHP_CENSOR_PROJECT_TITLE'));
        self::assertEquals('http://example.com/project/view/20', \getenv('PHP_CENSOR_PROJECT_LINK'));
        self::assertEquals(10, \getenv('PHP_CENSOR_BUILD_ID'));
        self::assertEquals('build_path', \getenv('PHP_CENSOR_BUILD_PATH'));
        self::assertEquals('http://example.com/build/view/10', \getenv('PHP_CENSOR_BUILD_LINK'));
        self::assertEquals('branch', \getenv('PHP_CENSOR_BRANCH'));
        self::assertEquals('branch_link', \getenv('PHP_CENSOR_BRANCH_LINK'));
        self::assertEquals('environment', \getenv('PHP_CENSOR_ENVIRONMENT'));
        self::assertEquals('1.0.0', \getenv('PHP_CENSOR_SYSTEM_VERSION'));
    }

    public function testRealtimeInterpolate(): void
    {
        $interpolator = new VariableInterpolator(
            new VariableInterpolatorBuild(),
            new VariableInterpolatorProject(),
            '1.0.0'
        );

        self::assertMatchesRegularExpression(
            '#CURRENT_DATE\: (\d{4}\-\d{2}\-\d{2})#',
            $interpolator->interpolate('Text with CURRENT_DATE: %CURRENT_DATE% for testing')
        );

        self::assertMatchesRegularExpression(
            '#CURRENT_DATETIME\: (\d{4}\-\d{2}\-\d{2}_\d{2}\-\d{2}\-\d{2})#',
            $interpolator->interpolate('Text with CURRENT_DATETIME: %CURRENT_DATETIME% for testing')
        );

        self::assertMatchesRegularExpression(
            '#CURRENT_TIME\: (\d{2}\-\d{2}\-\d{2})#',
            $interpolator->interpolate('Text with CURRENT_TIME: %CURRENT_TIME% for testing')
        );
    }

    public function testInterpolateWithEndingSlashInUrl(): void
    {
        $interpolator = new VariableInterpolator(
            new VariableInterpolatorBuild(),
            new VariableInterpolatorProject(),
            '1.0.0'
        );

        self::assertEquals(
            'Text with http://example.com/project/view/20 for testing.',
            $interpolator->interpolate('Text with %PROJECT_LINK% for testing.')
        );
    }
}
