<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildInterface
{
    public const STAGE_SETUP    = 'setup';
    public const STAGE_TEST     = 'test';
    public const STAGE_DEPLOY   = 'deploy';
    public const STAGE_COMPLETE = 'complete';
    public const STAGE_SUCCESS  = 'success';
    public const STAGE_FAILURE  = 'failure';
    public const STAGE_FIXED    = 'fixed';
    public const STAGE_BROKEN   = 'broken';

    public const STATUS_PENDING = 0;
    public const STATUS_RUNNING = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_FAILED  = 3;

    public const SOURCE_UNKNOWN                       = 0;
    public const SOURCE_MANUAL_WEB                    = 1;
    public const SOURCE_MANUAL_CONSOLE                = 2;
    public const SOURCE_PERIODICAL                    = 3;
    public const SOURCE_WEBHOOK_PUSH                  = 4;
    public const SOURCE_WEBHOOK_PULL_REQUEST_CREATED  = 5;
    public const SOURCE_WEBHOOK_PULL_REQUEST_UPDATED  = 6;
    public const SOURCE_WEBHOOK_PULL_REQUEST_APPROVED = 7;
    public const SOURCE_WEBHOOK_PULL_REQUEST_MERGED   = 8;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getProjectId(): int;

    /**
     * @return string
     */
    public function getCommitId(): string;

    /**
     * @return string
     */
    public function getCommitterEmail(): string;

    /**
     * @return string
     */
    public function getCommitMessage(): string;

    /**
     * @return string
     */
    public function getCommitLink(): string;

    /**
     * @return string
     */
    public function getBranch(): string;

    /**
     * @return string
     */
    public function getBranchLink(): string;

    /**
     * @return string
     */
    public function getTag(): string;

    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @return int
     */
    public function getSource(): int;

    /**
     * @return int
     */
    public function getUserId(): int;

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    public function getExtra(?string $key = null);

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return string
     */
    public function getLog(): string;

    /**
     * @return \DateTime|null
     */
    public function getCreateDate(): ?\DateTime;

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime;

    /**
     * @return \DateTime|null
     */
    public function getFinishDate(): ?\DateTime;

    /**
     * Returns absolute build path
     *
     * @return string
     */
    public function getBuildPath(): string;

    /**
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * @return string
     */
    public function getBuildDirectory(): string;

    /**
     * @return string
     */
    public function getBuildBranchDirectory(): string;

    /**
     * @return bool
     */
    public function isDebug(): bool;
}
