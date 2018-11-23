<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

interface BuildInterface
{
    const STAGE_SETUP    = 'setup';
    const STAGE_TEST     = 'test';
    const STAGE_DEPLOY   = 'deploy';
    const STAGE_COMPLETE = 'complete';
    const STAGE_SUCCESS  = 'success';
    const STAGE_FAILURE  = 'failure';
    const STAGE_FIXED    = 'fixed';
    const STAGE_BROKEN   = 'broken';

    const STATUS_PENDING = 0;
    const STATUS_RUNNING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED  = 3;

    const SOURCE_UNKNOWN                       = 0;
    const SOURCE_MANUAL_WEB                    = 1;
    const SOURCE_MANUAL_CONSOLE                = 2;
    const SOURCE_PERIODICAL                    = 3;
    const SOURCE_WEBHOOK_PUSH                  = 4;
    const SOURCE_WEBHOOK_PULL_REQUEST_CREATED  = 5;
    const SOURCE_WEBHOOK_PULL_REQUEST_UPDATED  = 6;
    const SOURCE_WEBHOOK_PULL_REQUEST_APPROVED = 7;
    const SOURCE_WEBHOOK_PULL_REQUEST_MERGED   = 8;

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
}
