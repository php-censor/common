<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

interface VariableInterpolatorInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function interpolate(string $string): string;
}
