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

use Gtt\ThriftGenerator\Reflection\ServiceReflection;
use Gtt\ThriftGenerator\Exception\ClassNotSpecifiedException;
use Gtt\ThriftGenerator\Transformer\ComplexTypeNameTransformer;
use Gtt\ThriftGenerator\Transformer\NamespaceTransformer;
use Gtt\ThriftGenerator\Transformer\ServiceNameTransformer;

use ReflectionClass;

/**
 * Thrift file generator
 * Supports only one service per thrift file for now
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ThriftGenerator extends AbstractGenerator
{
    /**
     * Target class reflection
     *
     * @var ReflectionClass
     */
    protected $classRef;

    /**
     * Service reflection
     *
     * @var ServiceReflection
     */
    protected $serviceReflection;

    /**
     * Sets class reflection for thrift file content generation
     *
     * @param ReflectionClass $classRef
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

        $this->serviceReflection = new ServiceReflection($this->classRef->getName());

        $namespace  = $this->generateNamespace();
        $structs    = $this->generateStructs();
        $exceptions = $this->generateExceptions();
        $services   = $this->generateServices();

        $search  = array("<namespace>", "<structs>", "<exceptions>", "<services>");
        $replace = array($namespace, $structs, $exceptions, $services);
        $file = str_replace($search, $replace, $this->getThriftTemplate());

        return $file;
    }

    /**
     * Returns thrift template
     *
     * @return string
     */
    protected function getThriftTemplate()
    {
        $thriftTemplate = <<<EOT
<namespace>

<structs>

<exceptions>

<services>
EOT;
        return $thriftTemplate;
    }

    /**
     * Generates thrift namespace
     *
     * @return string
     */
    protected function generateNamespace()
    {
        $generator = $this->getNamespaceGenerator();
        $generator->setClass($this->classRef);

        return $generator->generate();
    }

    /**
     * Generates thrift structs
     *
     * @return string
     */
    protected function generateStructs()
    {
        $generator = $this->getStructGenerator();
        $structs   = array();
        foreach ($this->serviceReflection->getStructs() as $structRef) {
            $generator->setClass($structRef);
            $structs[] = $generator->generate();
        }
        $structs = implode("\n", $structs);

        return $structs;
    }

    /**
     * Generates thrift exceptions
     *
     * @return string
     */
    protected function generateExceptions()
    {
        $generator  = $this->getExceptionGenerator();
        $exceptions = array();
        foreach ($this->serviceReflection->getExceptions() as $exceptionRef) {
            $generator->setClass($exceptionRef);
            $exceptions[] = $generator->generate();
        }
        $exceptions = implode("\n", $exceptions);

        return $exceptions;
    }

    /**
     * Generates thrift services
     *
     * @return string
     */
    protected function generateServices()
    {
        $generator = $this->getServiceGenerator();
        $generator->setService($this->serviceReflection);
        $service = $generator->generate();

        return $service;
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
            ->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Creates and returns service generator
     *
     * @return ServiceGenerator
     */
    protected function getServiceGenerator()
    {
        $generator = new ServiceGenerator();
        $generator
            ->setServiceNameTransformer(new ServiceNameTransformer())
            ->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Creates and returns struct generator
     *
     * @return StructGenerator
     */
    protected function getStructGenerator()
    {
        $generator = new StructGenerator();
        $generator
            ->setComplexTypeNameTransformer(
                new ComplexTypeNameTransformer($this->classRef->getNamespaceName())
            )
            ->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Creates and returns exception generator
     *
     * @return ExceptionGenerator
     */
    protected function getExceptionGenerator()
    {
        $generator = new ExceptionGenerator();
        $generator
            ->setComplexTypeNameTransformer(
                new ComplexTypeNameTransformer($this->classRef->getNamespaceName())
            )
            ->setIndentation($this->getIndentation());

        return $generator;
    }
}