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

use Gtt\ThriftGenerator\Generator\Exception\TargetNotSpecifiedException;
use Gtt\ThriftGenerator\Generator\Exception\TransformerNotSpecifiedException;
use Gtt\ThriftGenerator\Generator\Exception\UnsupportedDefaultValueException;
use Gtt\ThriftGenerator\Generator\Exception\PropertyTypeNotSetException;
use Gtt\ThriftGenerator\Transformer\TransformerInterface;
use Zend\Code\Reflection\PropertyReflection;
use ReflectionProperty;

/**
 * Struct/Exception properties generator
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class PropertyGenerator extends AbstractGenerator
{
    /**
     * Property reflection
     *
     * @var ReflectionProperty
     */
    protected $propertyRef;

    /**
     * Property default value
     *
     * @var mixed
     */
    protected $propertyDefaultValue = null;

    /**
     * Type transfer
     *
     * @var TransformerInterface
     */
    protected $typeTransformer;

    /**
     * Sets property reflection
     *
     * @param ReflectionProperty $propertyRef property reflection
     *
     * @return $this
     */
    public function setProperty(ReflectionProperty $propertyRef)
    {
        $this->propertyRef = $propertyRef;

        // introspect default value for property
        $parentClassDefaultProperties = $this->propertyRef->getDeclaringClass()->getDefaultProperties();
        $this->propertyDefaultValue   = $parentClassDefaultProperties[$this->propertyRef->getName()];

        return $this;
    }

    /**
     * Sets type transformer
     *
     * @param TransformerInterface $transformer type transformer
     *
     * @return $this
     */
    public function setTypeTransformer(TransformerInterface $transformer)
    {
        $this->typeTransformer = $transformer;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->propertyRef)) {
            throw new TargetNotSpecifiedException("property reflection", "class property", __CLASS__."::".__METHOD__);
        }

        $type    = $this->transformType($this->getPropertyType());
        $name    = $this->propertyRef->getName();
        $default = $this->generateDefault($name);

        $search   = array('<type>', '<name>', '<default>');
        $replace  = array($type, $name, $default);
        $property = str_replace($search, $replace, $this->getPropertyTemplate());

        return $property;
    }

    /**
     * Returns property template
     *
     * @return string
     */
    protected function getPropertyTemplate()
    {
        $propertyTemplate = <<<EOT
<type> <name><default>
EOT;
        return $propertyTemplate;
    }

    /**
     * Prepares and returns thrift property type
     *
     * @return string
     */
    protected function getPropertyType()
    {
        $propertyName      = $this->propertyRef->getName();
        $propertyClassName = $this->propertyRef->getDeclaringClass()->getName();
        $propertyType      = null;
        $zendPropertyRef   = new PropertyReflection($propertyClassName, $propertyName);
        /** @var \Zend\Code\Reflection\DocBlock\Tag\GenericTag $tag */
        foreach ($zendPropertyRef->getDocBlock()->getTags() as $tag) {
            if ($tag->getName() == 'var') {
                $propertyType = $tag->getContent();
                break;
            }
        }
        if (!$propertyType) {
            throw new PropertyTypeNotSetException($propertyName, $propertyClassName);
        }
        return $propertyType;
    }

    /**
     * Transforms PHP type to thrift type
     *
     * @param string $type PHP type
     *
     * @throws TransformerNotSpecifiedException is transformer is not specified
     *
     * @return string thrift type
     */
    protected function transformType($type)
    {
        if (!$this->typeTransformer) {
            throw new TransformerNotSpecifiedException("Type", $type);
        }
        return $this->typeTransformer->transform($type);
    }

    /**
     * Generate property default if it has the one
     *
     * @param string $propertyName property name
     *
     * @return string
     */
    protected function generateDefault($propertyName)
    {
        $default = "";
        if ($this->hasDefaultValue()) {
            $defaultValue = $this->generateDefaultValue(
                $this->propertyRef->getDeclaringClass()->getName(),
                $propertyName
            );
            $default = " = $defaultValue";
            return $default;
        }
        return $default;
    }

    /**
     * Checks that property has default value
     *
     * @return bool
     */
    protected function hasDefaultValue()
    {
        return !is_null($this->propertyDefaultValue);
    }

    /**
     * Generates property's default value
     *
     * @param string $className class name
     * @param string $propertyName property name
     *
     * @return string
     */
    protected function generateDefaultValue($className, $propertyName)
    {
        $generatedDefaultValue = $this->propertyDefaultValue;
        // TODO add support of arrays (are arrays allowed as default values in thrift?)
        $supportedDefaultValueTypes = array("boolean", "integer", "double", "string");
        $defaultValueType = gettype($this->propertyDefaultValue);
        if (!in_array($defaultValueType, $supportedDefaultValueTypes)) {
            throw new UnsupportedDefaultValueException($defaultValueType, $className, $propertyName);
        }
        if ($defaultValueType == "string") {
            // quoting string values
            $generatedDefaultValue = "\"$defaultValueType\"";
        }
        // TODO should we do something with booleans?

        return $generatedDefaultValue;
    }
}
