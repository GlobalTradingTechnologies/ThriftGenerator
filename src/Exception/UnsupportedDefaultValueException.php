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
 * Exception is thrown when class has property with default value which type is not supported
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class UnsupportedDefaultValueException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $type $unsupported type
     * @param string $className class name
     * @param string $propertyName property name
     */
    public function __construct($type, $className, $propertyName)
    {
        parent::__construct(
            sprintf(
                "Class %s contains property %s with default which type %s can not be recognized or is not supported",
                $className,
                $propertyName,
                $type
            )
        );
    }
}
