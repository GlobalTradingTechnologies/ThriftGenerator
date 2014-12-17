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

use Gtt\ThriftGenerator\Reflection\Exception\InvalidClassStructureException;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;
use Gtt\ThriftGenerator\Reflection\ComplexTypeReflection;
use Zend\Code\Reflection\MethodReflection;
use Zend\Server\Reflection as ZendServerReflection;
use Zend\Server\Reflection\ReflectionClass as ZendReflectionClass;
use Zend\Server\Reflection\Prototype as ZendReflectionPrototype;
use \ReflectionClass;

/**
 * Reflects PHP class in the terms of thrift service
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceReflection extends ComplexTypeReflection
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
     * @var ComplexTypeReflection[]
     */
    protected $exceptionRefs = array();

    /**
     * Introspected structs reflections from original class methods
     * Struct can be any DTO class used as method parameter, return value
     * or public property of another struct or exception
     *
     * @var ComplexTypeReflection[]
     */
    protected $structRefs = array();

    /**
     * Transitively fetched exceptions and structs from original class
     *
     * @var ComplexTypeReflection[]
     */
    protected $transitiveComplexTypeRefs;

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
     * @return ComplexTypeReflection[]
     */
    public function getExceptions()
    {
        return array_values($this->exceptionRefs);
    }

    /**
     * Returns original class structs
     *
     * @return ComplexTypeReflection[]
     */
    public function getStructs()
    {
        return array_values($this->structRefs);
    }

    /**
     * Returns transitively fetched exceptions and structs from original class
     * (For example if:
     *  1. Original service depends on A and B;
     *  2. A depends on C, D, E;
     *  3. B depends on F, D
     * method returns A, B, C, D, E, F
     *
     * @return ComplexTypeReflection[]
     */
    public function getTransitiveComplexTypes()
    {
        if (is_null($this->transitiveComplexTypeRefs)) {
            $complexTypeRefs = $this->exceptionRefs + $this->structRefs;
            $this->collectComplexTypeDependencies($complexTypeRefs);
            $this->transitiveComplexTypeRefs = array_values($complexTypeRefs);
        }

        return $this->transitiveComplexTypeRefs;
    }

    /**
     * Reflects original class methods to product method prototypes
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
                        $exceptions[$exceptionClass] = new ComplexTypeReflection($exceptionRef->getName());
                    } else {
                        throw new InvalidClassStructureException(
                            $this->classRef->getName(),
                            sprintf(
                                "Method %s has invalid exception %s" .
                                "Only instantiable subclassess of \Exception class are allowed.",
                                $prototype->getMethodReflection()->getName(),
                                $exceptionRef->getName()
                            )
                        );
                    }
                }
            }
        }
        $this->exceptionRefs = $exceptions;
    }

    /**
     * Reflects original class methods to collect structs used
     * Also fetches structs represented as public properties of already
     * collected struct or exception
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
                }
            }

            // collect method return
            $returnClassRef = $this->resolveParameterSingleClass($methodPrototype->getReturnType());
            if ($returnClassRef && !$returnClassRef->isInstantiable()) {
                // non-instantiable returns are not allowed
                // zend-reflection does not allow fetching method name from return reflection or method prototype
                throw new InvalidClassStructureException(
                    $this->classRef->getName(),
                    sprintf(
                        "Method %s has non-instantiable return value %s." .
                        "Only instantiable classes are allowed for method return values.",
                        $returnClassRef->getName(),
                        $methodPrototype->getMethodReflection()->getName()
                    )
                );
            }

            if ($returnClassRef && $returnClassRef->isInstantiable()) {
                $structRefs[$returnClassRef->getName()] = $returnClassRef;
            }
        }

        $this->structRefs = $structRefs;
    }

    /**
     * Recursively collects complex type dependencies
     *
     * @param ComplexTypeReflection[] &$complexTypeRefs list of already collected dependencies
     */
    private function collectComplexTypeDependencies(&$complexTypeRefs)
    {
        foreach ($complexTypeRefs as $complexTypeRef) {
            $dependencies = $complexTypeRef->getPropertyDependencies();
            foreach ($dependencies as $dependency) {
                if (!array_key_exists($dependency->getName(), $complexTypeRefs)) {
                    $complexTypeRefs[$dependency->getName()] = $dependency;
                    $this->collectComplexTypeDependencies($complexTypeRefs);
                }
            }
        }
    }
}
