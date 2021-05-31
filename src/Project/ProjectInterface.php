<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Project;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
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

    /**
     * Returns build configuration for the project.
     *
     * @return array
     */
    public function getBuildConfig(): array;

    /**
     * @return string
     */
    public function getLink(): string;
}
