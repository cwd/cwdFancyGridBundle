<?php
/*
 * This file is part of mailowl
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cwd\FancyGridBundle\Grid\Exception;

/**
 * Class UnexpectedTypeException
 * @package Cwd\FancyGridBundle\Grid\Exception
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class UnexpectedTypeException extends InvalidArgumentException
{
    /**
     * UnexpectedTypeException constructor.
     * @param string $value
     * @param int    $expectedType
     */
    public function __construct($value, $expectedType)
    {
        parent::__construct(
            sprintf('Expected argument of type "%s", "%s" given',
                $expectedType,
                is_object($value) ? get_class($value) : gettype($value)
            )
        );
    }
}
