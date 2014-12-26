<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 12/4/14
 */

namespace Gtt\ThriftGenerator\Generator\Exception;

/**
 * Exception for the case when thrift struct/exception property can bot be generated
 * since property type is not set in doc block
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class PropertyTypeNotSetException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $propertyName property name
     * @param string $className class name
     */
    public function __construct($propertyName, $className)
    {
        $message = sprintf("The type of property %s in %s class must be set in doc block", $propertyName, $className);
        parent::__construct($message);
    }
}