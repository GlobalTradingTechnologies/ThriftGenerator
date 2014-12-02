<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 11/25/14
 */

namespace Gtt\ThriftGenerator\Reflection;

use Gtt\ThriftGenerator\Exception\InvalidClassStructureException;
use Gtt\ThriftGenerator\TypeHelper;
use Zend\Code\Reflection\PropertyReflection;
use ReflectionClass;

/**
 * Reflects PHP class in the terms of thrift complex types (exceptions, structs)
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ComplexTypeReflection extends ReflectionClass
{
    /**
     * List of class complex type dependencies
     *
     * @var ComplexTypeReflection[]
     */
    protected $dependencies;

    /**
     * Returns list of depending complex types (which are complex type properties)
     *
     * @return ComplexTypeReflection[]
     */
    public function getPropertyDependencies()
    {
        if (is_null($this->dependencies)) {
            $this->dependencies = array();
            $this->collectStructsFromClass();
        }
        return $this->dependencies;
    }

    /**
    * Collects structs from current class
    */
    protected function collectStructsFromClass ()
    {
        $classProperties = $this->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($classProperties as $classProperty) {
            $propertyName = $classProperty->getName();
            //fetching type of class property stored in @var annotation
            // get property type
            $propertyType    = null;
            $zendPropertyRef = new PropertyReflection($this->getName(), $propertyName);
            foreach ($zendPropertyRef->getDocBlock()->getTags() as $tag) {
                if ($tag->getName() == 'var') {
                    $propertyType = $tag->getContent();
                    break;
                }
            }

            if (!$propertyType) {
                throw new InvalidClassStructureException($this->getName(), sprintf("The type of property %s must be set", $propertyName));
            }

            $propertyClass = $this->resolveParameterSingleClass($propertyType);
            if ($propertyClass) {
                // normalize property type
                $propertyType =  $propertyClass->getName();
                if (array_key_exists($propertyType, $this->dependencies)) {
                    continue;
                }
                if (!$propertyClass->isInstantiable()) {
                    throw new InvalidClassStructureException(
                        $this->getName(),
                        sprintf(
                            "Property %s has type %s that is not instantiable. Only instantiable types are allowed.",
                            $classProperty->getName(),
                            $propertyType
                        )
                    );
                }
                $this->dependencies[$propertyClass->getName()] = $propertyClass;
            }
        }
    }

    /**
     * Resolves parameter's single class
     * Fetches single types from lists (\Test\Class[]) or simply reflects
     * class (if it exists) of the type specified. Otherwise returns null
     *
     * @param string $type type
     *
     * @return ComplexTypeReflection|null
     */
    protected function resolveParameterSingleClass($type)
    {
        if (TypeHelper::isListType($type)) {
            $type = TypeHelper::getListSingleType($type);
        }
        if (class_exists($type) || interface_exists($type)) {
            return new ComplexTypeReflection($type);
        }
        return null;
    }
}
