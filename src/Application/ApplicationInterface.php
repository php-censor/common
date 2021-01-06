<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Application;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ApplicationInterface
{
    /**
     * Returns system (application) configuration.
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Example: /var/www/php-censor.local/
     *
     * @return string
     */
    public function getRootPath(): string;

    /**
     * Application config option: php-censor.build.allow_public_artifacts
     *
     * @return bool
     */
    public function isPublicArtifactsAllowed(): bool;

    /**
     * Example: http://php-censor.local/artifacts/
     *
     * @return string
     */
    public function getArtifactsLink(): string;

    /**
     * Example: /var/www/php-censor.local/public/artifacts/
     *
     * @return string
     */
    public function getArtifactsPath(): string;
}
