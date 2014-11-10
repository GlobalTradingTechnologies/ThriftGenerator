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

use Gtt\ThriftGenerator\Exception\UnsupportedTypeException;

/**
 * Transforms PHP types into thrift types and provides some useful function to type introspection
 * Handles primitive types, arrays (transforms them as array of strings), complex types (classes)
 * and lists of another types (for example \Test\Class[])
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TypeHandler
{
    /**
     * Map of PHP primitive types and thrift base types
     *
     * @var array
     */
    protected static $baseTypeMap = array(
        // special type - void
        'void' => 'void',
        // simple types
        'bool' => 'bool',
        'boolean' => 'bool',
        'float' => 'double',
        'int' => 'i32',
        'integer' => 'i32',
        'string' => 'string',
        // map array to list of strings since in php we can not recognize the type of array elements from doc comments and method signature
        'array' => "list<string>"
    );

    /**
     * Transforms php type to thrift's type
     *
     * TODO add associative array (map) support if it possible?
     * (now in php we can not recognize the associativeness of array elements from doc comments and method signature)?
     *
     * @param string $type type to transform
     *
     * @return string
     */
    public static function transform($type)
    {
        if (static::isListType($type)) {
            $listElementsType = static::getListSingleType($type);
            $translatedType   = sprintf("list<%s>", static::transformSingleType($listElementsType));

            return $translatedType;
        }
        return static::transformSingleType($type);
    }

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

    /**
     * Transforms single (not list) php type to thrift's type
     *
     * @param string $type single PHP type
     *
     * @return string
     */
    protected static function transformSingleType($type)
    {
        if (array_key_exists($type, static::$baseTypeMap)) {
            $thriftPropertyType = static::$baseTypeMap[$type];
        } elseif (class_exists($type)) {
            // TODO do some complex type transformation?
            $thriftPropertyType = $type;
        } else {
            throw new UnsupportedTypeException($type);
        }
        return $thriftPropertyType;
    }
}
