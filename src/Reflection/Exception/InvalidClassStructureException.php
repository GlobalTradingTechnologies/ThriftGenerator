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

namespace Gtt\ThriftGenerator\Reflection\Exception;

/**
 * Exception is thrown when class cannot be properly reflected due to its unsupported or invalid structure
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class InvalidClassStructureException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $className class name
     * @param string $message message
     */
    public function __construct($className, $message)
    {
        $message = "Class $className cannot be handled due to it's unsupported signature. $message";

        parent::__construct($message);
    }
}
 