<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

use PHPCensor\Common\Exception\Exception;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ViewFactoryInterface
{
    /**
     * @param string      $viewPath
     * @param string|null $viewExtension
     *
     * @return ViewInterface
     *
     * @throws Exception
     */
    public function createView(string $viewPath, ?string $viewExtension = null): ViewInterface;
}
