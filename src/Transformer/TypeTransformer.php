<?php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/14/14
 */

namespace Gtt\ThriftGenerator\Transformer;

use Gtt\ThriftGenerator\Exception\UnsupportedTypeException;
use Gtt\ThriftGenerator\TypeHelper;

/**
 * Translates php types to thrift types
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TypeTransformer implements TransformerInterface
{
    /**
     * Map of PHP primitive types and thrift base types
     *
     * @var array
     */
    protected static $baseTypeMap = array(
        // special type - void
        'void'    => 'void',
        // simple types
        'bool'    => 'bool',
        'boolean' => 'bool',
        'float'   => 'double',
        'double'  => 'double',
        'int'     => 'i32',
        'integer' => 'i32',
        'string'  => 'string',
        // map array to list of strings since in php we can not recognize the type of array elements from doc comments and method signature
        'array'   => "list<string>"
    );

    /**
     * Complex type transformer
     *
     * @var TransformerInterface
     */
    protected $complexTypeTransformer;

    /**
     * Constructor
     *
     * @param TransformerInterface $complexTypeTransformer complex type transformer
     */
    public function __construct(TransformerInterface $complexTypeTransformer)
    {
        $this->complexTypeTransformer = $complexTypeTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entity)
    {
        if (TypeHelper::isListType($entity)) {
            $listElementsType = TypeHelper::getListSingleType($entity);
            $translatedType   = sprintf("list<%s>", $this->transformSingleType($listElementsType));

            return $translatedType;
        }
        return $this->transformSingleType($entity);
    }

    /**
     * Transforms single (not list) php type to thrift's type
     *
     * @param string $type single PHP type

     * @throws UnsupportedTypeException when type is not supported
     *
     * @return string
     */
    protected function transformSingleType($type)
    {
        if (array_key_exists($type, static::$baseTypeMap)) {
            $thriftPropertyType = static::$baseTypeMap[$type];
        } elseif (class_exists($type)) {
            $thriftPropertyType = $this->complexTypeTransformer->transform($type);
        } else {
            throw new UnsupportedTypeException($type);
        }
        return $thriftPropertyType;
    }
}
