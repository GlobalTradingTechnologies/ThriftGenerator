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

namespace Gtt\ThriftGenerator\Reflection;

use Gtt\ThriftGenerator\Exception\InvalidClassStructureException;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;

use Gtt\ThriftGenerator\TypeHelper;
use Zend\Code\Reflection\MethodReflection;

use Zend\Code\Reflection\PropertyReflection;
use Zend\Server\Reflection as ZendServerReflection;
use Zend\Server\Reflection\ReflectionClass as ZendReflectionClass;
use Zend\Server\Reflection\Prototype as ZendReflectionPrototype;

use \ReflectionClass;

/**
 * Reflects PHP class in the terms of thrift service
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceReflection extends ReflectionClass
{
    /**
     * Original class reflection
     *
     * @var ReflectionClass
     */
    protected $classRef = null;

    /**
     * Original class method prototypes
     *
     * @var MethodPrototype[]
     */
    protected $methodPrototypes = array();

    /**
     * Introspected exception reflections from original class methods
     *
     * @var ReflectionClass[]
     */
    protected $exceptionRefs = array();

    /**
     * Introspected structs reflections from original class methods
     * Struct can be any DTO class used as method parameter, return value
     * or public property of another struct or exception
     *
     * @var ReflectionClass[]
     */
    protected $structRefs = array();

    /**
     * Constructor
     *
     * @param string $class class name
     */
    public function __construct($class)
    {
        parent::__construct($class);

        $this->classRef = new \ReflectionClass($class);
        $this->reflectMethodPrototypes();
        $this->reflectExceptions();
        $this->reflectStructs();
    }

    /**
     * Returns original class method prototypes
     *
     * @return MethodPrototype[]
     */
    public function getMethodPrototypes()
    {
        return $this->methodPrototypes;
    }

    /**
     * Returns original class exceptions can be thrown from its methods
     *
     * @return \ReflectionClass[]
     */
    public function getExceptions()
    {
        return $this->exceptionRefs;
    }

    /**
     * Returns original class structs
     *
     * @return \ReflectionClass[]
     */
    public function getStructs()
    {
        return $this->structRefs;
    }

    /**
     * Reflects original class methods to product method prototypes
     *
     * @return ZendReflectionPrototype[]
     */
    protected function reflectMethodPrototypes()
    {
        /** @var ZendReflectionClass|ReflectionClass $zendClassRef */
        $zendClassRef = ZendServerReflection::reflectClass($this->classRef->getName());
        /** @var \Zend\Server\Reflection\ReflectionMethod|\ReflectionMethod $zendServerMethodRef */
        foreach ($zendClassRef->getMethods() as $zendServerMethodRef) {
            // Only one prototype is supported: the one with the maximum number of arguments
            $prototype = null;
            $maxNumArgumentsOfPrototype = -1;
            foreach ($zendServerMethodRef->getPrototypes() as $tmpPrototype) {
                $numParams = count($tmpPrototype->getParameters());
                if ($numParams > $maxNumArgumentsOfPrototype) {
                    $maxNumArgumentsOfPrototype = $numParams;
                    $prototype = $tmpPrototype;
                }
            }
            if ($prototype === null) {
                throw new InvalidClassStructureException(
                    $this->classRef->getName(),
                    sprintf(
                        "Method %s has doc block that cannot be successfully processed",
                        $zendServerMethodRef->getName()
                    )
                );
            }

            // introspect exceptions
            $exceptionRefs = array();
            $methodZendRef = new MethodReflection($this->classRef->getName(), $zendServerMethodRef->getName());
            $methodDoc     = $methodZendRef->getDocBlock();
            $exceptionTags = $methodDoc->getTags('throws');
            /** @var \Zend\Code\Reflection\DocBlock\Tag\ThrowsTag $exceptionTag */
            foreach ($exceptionTags as $exceptionTag) {
                $types = $exceptionTag->getTypes();
                // multiple types are not allowed in @throws tags
                if (count($types) > 1) {
                    throw new InvalidClassStructureException(
                        $this->classRef->getName(),
                        sprintf(
                            "Method %s has exception type alternation. Only one is allowed",
                            $zendServerMethodRef->getName()
                        )
                    );
                }
                $exceptionRefs[] = new ReflectionClass($types[0]);
            }

            //use method prototype with support of exceptions
            $prototype = new MethodPrototype(
                new \ReflectionMethod($this->classRef->getName(), $zendServerMethodRef->getName()),
                $prototype->getReturnValue(),
                $prototype->getParameters(),
                $exceptionRefs
            );
            $this->methodPrototypes[] = $prototype;
        }
    }

    /**
     * Reflects original class methods to collect exceptions can be thrown
     *
     * @return ReflectionClass[] list of exception classes refs
     */
    protected function reflectExceptions()
    {
        $exceptions = array();
        foreach ($this->methodPrototypes as $prototype) {
            /** @var \ReflectionClass $exceptionRef */
            foreach ($prototype->getExceptions() as $exceptionRef) {
                $exceptionClass = $exceptionRef->getName();
                if (!array_key_exists($exceptionClass, $exceptions)) {
                    // TODO need to check what kind of exceptions are allowed in thrift
                    // may be we should check that exception classes are user defined
                    if ($exceptionRef->isInstantiable() && $exceptionRef->isSubclassOf("\Exception")) {
                        $exceptions[$exceptionClass] = $exceptionRef;
                    } else {
                        throw new InvalidClassStructureException(
                            $this->classRef->getName(),
                            sprintf(
                                "Method %s has invalid exception %s" .
                                "Only instantiable subclassess of classes \Exception class are allowed.",
                                $prototype->getMethodReflection()->getName(),
                                $exceptionRef->getName()
                            )
                        );
                    }
                }
            }
        }
        $this->exceptionRefs = array_values($exceptions);
    }

    /**
     * Reflects original class methods to collect structs used
     * Also fetches structs represented as public properties of already
     * collected struct or exception
     *
     * @return \ReflectionClass[]
     */
    protected function reflectStructs()
    {
        $structRefs = array();
        /** @var ZendReflectionPrototype[] $methodPrototypes */
        foreach ($this->methodPrototypes as $methodPrototype) {
            // collect method params
            foreach ($methodPrototype->getParameters() as $parameterRef) {
                /** @var ReflectionClass $parameterClassRef */
                $parameterClassRef = $this->resolveParameterSingleClass($parameterRef->getType());
                if ($parameterClassRef && !isset($structRefs[$parameterClassRef->getName()])) {
                    if (!$parameterClassRef->isInstantiable()) {
                        // non-instantiable params are not allowed
                        throw new InvalidClassStructureException(
                            $this->classRef->getName(),
                            sprintf(
                                "Method %s has invalid typehint %s for parameter %s. " .
                                "Only instantiable classes are allowed for typehints.",
                                $parameterRef->getDeclaringFunction()->getName(),
                                $parameterClassRef->getName(),
                                $parameterRef->getName()
                            )
                        );
                    }
                    $structRefs[$parameterClassRef->getName()] = $parameterClassRef;
                    $this->collectStructsFromClass($parameterClassRef, $structRefs);
                }
            }

            //collect method return
            $returnClassRef = $this->resolveParameterSingleClass($methodPrototype->getReturnType());
            if ($returnClassRef && !$returnClassRef->isInstantiable()) {
                // non-instantiable returns are not allowed
                // zend-reflection does not allow fetching method name from return reflection or method prototype
                throw new InvalidClassStructureException(
                    $this->classRef->getName(),
                    sprintf(
                        "Method %s has non-instantiable return value %s" .
                        "Only instantiable classes are allowed for method return values.",
                        $returnClassRef->getName(),
                        $methodPrototype->getMethodReflection()->getName()
                    )
                );
            }

            if ($returnClassRef && $returnClassRef->isInstantiable()) {
                $structRefs[$returnClassRef->getName()] = $returnClassRef;
                $this->collectStructsFromClass($returnClassRef, $structRefs);
            }
        }

        // collect structs from exceptions
        foreach ($this->exceptionRefs as $exceptionRef) {
            $this->collectStructsFromClass($exceptionRef, $structRefs);
        }

        $this->structRefs = array_values($structRefs);
    }

    /**
     * Collects structs from already collected struct and puts it into $structRefs parameter specified
     *
     * @param ReflectionClass $classRef class reflection
     * @param ReflectionClass[] &$structRefs list of already collected structs
     *
     * @return void
     */
    private function collectStructsFromClass (ReflectionClass $classRef, &$structRefs)
    {
        $classProperties = $classRef->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($classProperties as $classProperty) {
            $propertyName = $classProperty->getName();
            //fetching type of class property stored in @var annotation
            // get property type
            $propertyType = null;
            $zendPropertyRef = new PropertyReflection($classRef->getName(), $propertyName);
            foreach ($zendPropertyRef->getDocBlock()->getTags() as $tag) {
                if ($tag->getName() == 'var') {
                    $propertyType = $tag->getContent();
                    break;
                }
            }

            if (!$propertyType) {
                throw new InvalidClassStructureException(
                    $classRef->getName(),
                    sprintf(
                        "The type of property %s for complex type %s must be set",
                        $propertyName,
                        $this->classRef->getName()
                    )
                );
            }

            $propertyClass = $this->resolveParameterSingleClass($propertyType);
            if ($propertyClass) {
                // normalize property type
                $propertyType =  $propertyClass->getName();
                if (array_key_exists($propertyType, $structRefs)) {
                    continue;
                }
                if (!$propertyClass->isInstantiable()) {
                    throw new InvalidClassStructureException(
                        $classRef->getName(),
                        sprintf(
                            "Property %s has type %s that is not instantiable. Only instantiable types are allowed.",
                            $classProperty->getName(),
                            $propertyType
                        )
                    );
                }
                $structRefs[$propertyType] = $propertyClass;
                $this->collectStructsFromClass($propertyClass, $structRefs);
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
     * @return ReflectionClass|null
     */
    private function resolveParameterSingleClass($type)
    {
        if (TypeHelper::isListType($type)) {
            $type = TypeHelper::getListSingleType($type);
        }
        if (class_exists($type) || interface_exists($type)) {
            return new ReflectionClass($type);
        }
        return null;
    }
}
