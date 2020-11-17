<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Repository;

use PHPCensor\Common\Build\BuildMetaInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaRepositoryInterface
{
    /**
     * @param int    $buildId
     * @param string $key
     *
     * @return BuildMetaInterface|null
     */
    public function getOneByBuildIdAndKey(
        int $buildId,
        string $key
    ): ?BuildMetaInterface;
}
