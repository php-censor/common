<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Exception\Exception;

interface PluginInterface
{
    /**
     * @throws \Exception
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function execute(): bool;
}
