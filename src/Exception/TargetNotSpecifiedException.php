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
 * Exception is thrown when target entity/entities are not specified before generation process is started
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TargetNotSpecifiedException extends \RuntimeException
{
    /**
     * Constructs exception
     *
     * @param string $target target that must be specified in order to allow generation process
     * @param int $generationType generation process type
     * @param string $specifierMethod method should be used to specify the target
     */
    public function __construct($target, $generationType, $specifierMethod = null)
    {
        $message = "$generationType generation process requires $target to be specified";
        if ($specifierMethod) {
            $message .= " via $specifierMethod method.";
        }
        parent::__construct(ucfirst($message));
    }
}
 