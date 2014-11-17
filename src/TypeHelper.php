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

namespace Gtt\ThriftGenerator;

/**
 * Holds some helpful methods to interact with PHP types
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TypeHelper
{
    /**
     * Checks that PHP type is list of other single types (for example \Test\Class[])
     *
     * @param string $type PHP type
     *
     * @return bool
     */
    public static function isListType($type)
    {
        return substr($type, -2) == "[]";
    }

    /**
     * Fetches type of elements from list type
     *
     * @param string $type list type
     *
     * @return string
     */
    public static function getListSingleType($type)
    {
        return substr($type, 0, strlen($type) - 2);
    }
}
