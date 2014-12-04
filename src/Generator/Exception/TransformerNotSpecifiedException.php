<?php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/17/14
 */

namespace Gtt\ThriftGenerator\Generator\Exception;

/**
 * Exception is thrown when operation can not be succeeded since transformer is not specified
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TransformerNotSpecifiedException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $entity entity that cannot be processed
     * @param string $type type of transformation can not be processed
     */
    public function __construct($entity, $type)
    {
        parent::__construct("$type transformation of $entity can not be processed since corresponding transformer is not specified");
    }
}
