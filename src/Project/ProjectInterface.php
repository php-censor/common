<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Project;

interface ProjectInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getTitle(): string;
}
