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

use Gtt\ThriftGenerator\Dumper\NamespacedComplexTypeDumper;
use Gtt\ThriftGenerator\Dumper\ServiceDumper;
use Gtt\ThriftGenerator\Generator\Exception\InvalidArgumentException;
use Gtt\ThriftGenerator\Generator\Exception\TargetNotSpecifiedException;
use Gtt\ThriftGenerator\Reflection\ServiceReflection;
use Gtt\ThriftGenerator\Transformer\ClassNameTransformer;
use Gtt\ThriftGenerator\Transformer\NamespaceTransformer;
use Gtt\ThriftGenerator\Reflection\ComplexTypeReflection;
use ReflectionClass;

/**
 * Thrift definition files generator
 * Generates list of thrift definition files based on list of PHP reflection classes specified
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ThriftGenerator
{
    /**
     * Target classes reflection list
     *
     * @var ReflectionClass[]
     */
    protected $classRefs = array();

    /**
     * Service reflection
     *
     * @var ServiceReflection
     */
    protected $serviceReflection;

    /**
     * Directory used to write output thrift definition files
     *
     * @var string
     */
    protected $outputDir;

    /**
     * Indentation
     *
     * @var string
     */
    protected $indentation = "    ";

    /**
     * Sets indentation
     *
     * @param string $indentation indentation
     *
     * @return $this
     */
    public function setIndentation($indentation)
    {
        $this->indentation = (string) $indentation;
        return $this;
    }

    /**
     * Sets class reflections for thrift file content generation
     *
     * @param ReflectionClass[] $classRefs list of class reflections
     *
     * @return $this
     */
    public function setClasses(array $classRefs)
    {
        foreach ($classRefs as $classRef) {
            $name = $classRef->getName();
            if (isset($this->classRefs[$name])) {
                continue;
            }
            if (!$classRef instanceof ReflectionClass) {
                throw new InvalidArgumentException("All the classes to be handled by ".__CLASS__." specified by ".
                __METHOD__. "must be instances of ReflectionClass");
            }
            $this->classRefs[$name] = $classRef;
        }
        return $this;
    }

    /**
     * Sets output directory path
     *
     * @param string $dir path to output directory
     *
     * @return $this
     */
    public function setOutputDir($dir)
    {
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new InvalidArgumentException("Output directory $dir must exist and be writable");
        }
        $this->outputDir = $dir;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->classRefs)) {
            throw new TargetNotSpecifiedException("domain classes reflections", "thrift definitions", __CLASS__."::".__METHOD__);
        }

        $serviceReflections = array();
        foreach ($this->classRefs as $classRef) {
            $serviceReflections[] = new ServiceReflection($classRef->getName());
        }

        // complex types
        $complexTypes = $this->generateComplexTypes($serviceReflections);
        $this->dumpComplexTypes($complexTypes);

        // services
        $services = $this->generateServices($serviceReflections);
        $this->dumpServices($services);
    }

    /**
     * Groups complex types used in original classes by it's namespaces and generates
     * thrift definition files for them
     *
     * @param ServiceReflection[] $serviceReflections list of original services reflections
     *
     * @return array associative array of complex type's generated definition files
     * indexed by original complex type's namespaces
     */
    protected function generateComplexTypes(array $serviceReflections = array())
    {
         // collect structs and exceptions from service reflection
        /** @var ComplexTypeReflection[] $complexTypeRefs */
        $complexTypeRefs = array();
        foreach ($serviceReflections as $serviceReflection) {
            /** @var ComplexTypeReflection[] $complexTypes list of service complex types */
            $complexTypes = array_merge($serviceReflection->getStructs(), $serviceReflection->getExceptions());
            // add to list of complex types only unique complex types
            foreach ($complexTypes as $complexType) {
                $name = $complexType->getName();
                if (!isset($complexTypeRefs[$name])) {
                    $complexTypeRefs[$name] = $complexType;
                }
            }
        }

        // collect recursively all the complex types used inside original classes specified
        $this->collectComplexTypeDependencies($complexTypeRefs);

        // group complex types by namespace
        $namespacedComplexTypeRefs = array();
        foreach ($complexTypeRefs as $complexTypeRef) {
            $complexTypeNamespace = $complexTypeRef->getNamespaceName();
            if (array_key_exists($complexTypeNamespace, $namespacedComplexTypeRefs)) {
                $namespacedComplexTypeRefs[$complexTypeNamespace][] = $complexTypeRef;
            } else {
                $namespacedComplexTypeRefs[$complexTypeNamespace] = array($complexTypeRef);
            }
        }

        // generate complex type list for each namespace
        $complexTypeListGenerator = $this->getComplexTypeListGenerator();
        $complexTypes             = array();
        foreach ($namespacedComplexTypeRefs as $namespace => $complexTypeRefs) {
            $complexTypeListGenerator->setComplexTypesNamespace($namespace);
            $complexTypeListGenerator->setComplexTypeRefs($complexTypeRefs);
            $complexTypes[$namespace] = $complexTypeListGenerator->generate();
        }

        return $complexTypes;
    }

    /**
     * Generates thrift services
     *
     * @param ServiceReflection[] $serviceReflections list of original services reflections
     *
     * @return array associative array of service's generated definition files indexed by originals FQCN's
     */
    protected function generateServices(array $serviceReflections = array())
    {
        $services = array();
        foreach ($serviceReflections as $serviceReflection) {
            $generator = $this->getServiceGenerator($serviceReflection);
            $generator->setService($serviceReflection);
            $services[$serviceReflection->getName()] = $generator->generate();
        }

        return $services;
    }

    /**
     * Creates and returns namespace generator
     *
     * @return NamespaceGenerator
     */
    protected function getNamespaceGenerator()
    {
        $generator = new NamespaceGenerator();
        $generator
            ->setNamespaceTransformer(new NamespaceTransformer())
            ->setIndentation($this->indentation);

        return $generator;
    }

    /**
     * Creates and returns service generator
     *
     * @param ServiceReflection $serviceReflection service reflection
     *
     * @return ServiceGenerator
     */
    protected function getServiceGenerator(ServiceReflection $serviceReflection)
    {
        $serviceNameTransformer = new ClassNameTransformer();
        $serviceNameTransformer->setCurrentNamespace($serviceReflection->getNamespaceName());

        $generator = new ServiceGenerator();
        $generator
            ->setServiceNameTransformer($serviceNameTransformer)
            ->setIndentation($this->indentation);

        return $generator;
    }

    /**
     * Creates and returns complex type list generator
     *
     * @return NamespacedComplexTypeListGenerator
     */
    protected function getComplexTypeListGenerator()
    {
        $generator = new NamespacedComplexTypeListGenerator();
        $generator->setIndentation($this->indentation);

        return $generator;
    }

    /**
     * Dumps complex types definitions into output dir
     *
     * @param array $complexTypes list of thrift definitions indexed by original complex types namespaces
     */
    protected function dumpComplexTypes(array $complexTypes = array())
    {
        $dumper = $this->getComplexTypeDumper();
        foreach ($complexTypes as $namespace => $definition) {
            $dumper
                ->setNamespace($namespace)
                ->setComplexTypesDefinition($definition);
            $dumper->dump();
        }
    }

    /**
     * Dumps complex types definitions into output dir
     *
     * @param array $services associative array of service's generated definition files indexed by originals FQCN's
     */
    protected function dumpServices(array $services = array())
    {
        $dumper = $this->getServiceDumper();
        foreach ($services as $name => $definition) {
            $dumper
                ->setServiceName($name)
                ->setServiceDefinition($definition);
            $dumper->dump();
        }
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

    /**
     * Creates and returns complex type dumper
     *
     * @return NamespacedComplexTypeDumper
     */
    protected function getComplexTypeDumper()
    {
        $dumper = new NamespacedComplexTypeDumper();
        $dumper->setOutputDir($this->outputDir);

        return $dumper;
    }

    /**
     * Creates and returns service dumper
     *
     * @return ServiceDumper
     */
    protected function getServiceDumper()
    {
        $dumper = new ServiceDumper();
        $dumper->setOutputDir($this->outputDir);
        
        return $dumper;
    }
}