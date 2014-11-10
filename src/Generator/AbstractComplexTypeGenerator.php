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

namespace Gtt\ThriftGenerator\Generator;

use Gtt\ThriftGenerator\Exception\ClassNotSpecifiedException;

use ReflectionClass;

/**
 * Abstract class for generation of complex types
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
abstract class AbstractComplexTypeGenerator extends AbstractGenerator
{
    /**
     * Original class reflection
     *
     * @var ReflectionClass
     */
    protected $classRef = null;

    /**
     * Sets complex type reflection
     *
     * @param ReflectionClass $classRef complex type class reflection
     *
     * @return $this
     */
    public function setClass(ReflectionClass $classRef)
    {
        $this->classRef = $classRef;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->classRef)) {
            throw new ClassNotSpecifiedException("Class to be handled is not specified");
        }

        $indentation = $this->getIndentation();
        $properties  = array();
        $generator   = $this->getPropertyGenerator();
        $identifier  = 0;

        foreach ($this->classRef->getProperties(\ReflectionProperty::IS_PUBLIC) as $propertyRef) {
            $identifier += 1;
            $generator->setProperty($propertyRef);
            $property     = $generator->generate();
            $property     = "$identifier: $property";
            $properties[] = $indentation . $property;
        }

        $properties = implode($properties, ",\n");
        $name       = $this->classRef->getName();

        $search      = array("<name>", "<properties>");
        $replace     = array($name, $properties);
        $complexType = str_replace($search, $replace, $this->getComplexTypeTemplate());

        return $complexType;
    }

    /**
     * Returns complex type template
     *
     * @return string
     */
    protected abstract function getComplexTypeTemplate();

    /**
     * Creates property generator
     *
     * @return PropertyGenerator
     */
    protected function getPropertyGenerator()
    {
        $generator = new PropertyGenerator();
        $generator->setIndentation($this->getIndentation());

        return $generator;
    }
}
