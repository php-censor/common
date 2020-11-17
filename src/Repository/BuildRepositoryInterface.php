<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Repository;

use PHPCensor\Common\Build\BuildInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildRepositoryInterface
{
    /**
     * @param int    $projectId
     * @param string $branch
     *
     * @return BuildInterface|null
     */
    public function getLatestByProjectAndBranch(
        int $projectId,
        string $branch
    ): ?BuildInterface;
}
