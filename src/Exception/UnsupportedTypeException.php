<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 10/16/14
 */

namespace Gtt\ThriftGenerator\Exception;

/**
 * Exception is thrown for the PHP types that could not be properly translated to thrift types
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class UnsupportedTypeException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $type unsupported type
     */
    public function __construct($type)
    {
        parent::__construct(sprintf("The type %s can not be recognized or is unsupported", $type));
    }
}
