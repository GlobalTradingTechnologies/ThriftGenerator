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
use Gtt\ThriftGenerator\Reflection\ServiceReflection;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;
use Gtt\ThriftGenerator\Transformer\ClassNameTransformer;
use Gtt\ThriftGenerator\Transformer\NamespaceTransformer;
use Gtt\ThriftGenerator\Transformer\TransformerInterface;
use Gtt\ThriftGenerator\Transformer\TypeTransformer;

/**
 * Generates thrift service
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceGenerator extends AbstractGenerator
{
    /**
     * Service reflection
     *
     * @var ServiceReflection
     */
    protected $serviceReflection;

    /**
     * Transformer service name
     *
     * @var TransformerInterface
     */
    protected $serviceNameTransformer;

    /**
     * Sets service
     *
     * @param ServiceReflection $serviceReflection service reflection
     *
     * @return $this
     */
    public function setService(ServiceReflection $serviceReflection)
    {
        $this->serviceReflection = $serviceReflection;

        return $this;
    }

    /**
     * Returns service reflection
     *
     * @return ServiceReflection
     */
    public function getService()
    {
        return $this->serviceReflection;
    }

    /**
     * Sets service name transformer
     *
     * @param TransformerInterface $transformer service name transformer
     *
     * @return $this
     */
    public function setServiceNameTransformer(TransformerInterface $transformer)
    {
        $this->serviceNameTransformer = $transformer;

        return $this;
    }

    /**
     * Returns service name transformer
     *
     * @return TransformerInterface
     */
    public function getServiceNameTransformer()
    {
        return $this->serviceNameTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->serviceReflection)) {
            throw new TargetNotSpecifiedException("service reflection", "thrift service", __CLASS__."::".__METHOD__);
        }

        $includes  = $this->generateIncludes();
        $namespace = $this->generateNamespace();
        $name      = $this->transformName($this->serviceReflection->getName());
        $methods   = $this->generateMethods();

        $search  = array("<includes>", "<namespace>", "<name>", "<methods>");
        $replace = array($includes, $namespace, $name, $methods);
        $service = trim(str_replace($search, $replace, $this->getServiceTemplate()));

        return $service;
    }

    /**
     * Returns service template
     *
     * @return string
     */
    protected function getServiceTemplate()
    {
        $serviceTemplate = <<<EOT
<includes>

<namespace>

service <name> {
<methods>
}
EOT;
        return $serviceTemplate;
    }

    /**
     * Creates and returns method generator
     *
     * @return MethodGenerator
     */
    protected function getMethodGenerator()
    {
        $complexTypeTransformer = new ClassNameTransformer();
        $complexTypeTransformer->setCurrentNamespace($this->serviceReflection->getNamespaceName());

        $typeTransformer = new TypeTransformer($complexTypeTransformer);

        $generator = new MethodGenerator();
        $generator
            ->setTypeTransformer($typeTransformer)
            ->setIndentation($this->getIndentation());
        return $generator;
    }

    /**
     * Creates and returns include list generator
     *
     * @return IncludeListGenerator
     */
    protected function getIncludeListGenerator()
    {
        $generator = new IncludeListGenerator();
        $generator->setIndentation($this->getIndentation());

        return $generator;
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
     * Transforms service name
     *
     * @param string $name service name
     *
     * @throws TransformerNotSpecifiedException is transformer is not specified
     *
     * @return string
     */
    protected function transformName($name)
    {
        if (!$this->serviceNameTransformer) {
            throw new TransformerNotSpecifiedException("Service name", $name);
        }
        return $this->serviceNameTransformer ? $this->serviceNameTransformer->transform($name) : $name;
    }

    /**
     * Generates service methods
     *
     * @return string
     */
    protected function generateMethods()
    {
        $generator = $this->getMethodGenerator();
        $methods   = array();
        /** @var MethodPrototype $methodPrototype */
        foreach ($this->serviceReflection->getMethodPrototypes() as $methodPrototype) {
            $generator->setMethodPrototype($methodPrototype);
            $methods[] = $this->getIndentation() . $generator->generate();
        }
        $methods = implode(",\n", $methods);
        return $methods;
    }

    /**
     * Generates includes
     *
     * @return string
     */
    protected function generateIncludes()
    {
        $generator = $this->getIncludeListGenerator();
        $generator->setUsedNamespacesFromServiceReflection($this->serviceReflection);
        $includes = $generator->generate();

        return $includes;
    }

    /**
     * Generates service namespace
     *
     * @return string
     */
    protected function generateNamespace()
    {
        $generator = $this->getNamespaceGenerator();
        $generator->setNamespace($this->serviceReflection->getNamespaceName());
        $namespace = $generator->generate();

        return $namespace;
    }
}
