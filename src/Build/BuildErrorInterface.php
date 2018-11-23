<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

interface BuildErrorInterface
{
    const SEVERITY_CRITICAL = 0;
    const SEVERITY_HIGH     = 1;
    const SEVERITY_NORMAL   = 2;
    const SEVERITY_LOW      = 3;
}
