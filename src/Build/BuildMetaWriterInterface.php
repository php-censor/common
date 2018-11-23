<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

interface BuildMetaWriterInterface
{
    /**
     * @param int    $buildId
     * @param string $key
     * @param string $value
     */
    public function write(
        int $buildId,
        string $key,
        string $value
    );
}
