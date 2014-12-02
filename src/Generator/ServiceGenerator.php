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

use Gtt\ThriftGenerator\Exception\TargetNotSpecifiedException;
use Gtt\ThriftGenerator\Exception\TransformerNotSpecifiedException;
use Gtt\ThriftGenerator\Reflection\ServiceReflection;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;
use Gtt\ThriftGenerator\Transformer\ComplexTypeNameTransformer;
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
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->serviceReflection)) {
            throw new TargetNotSpecifiedException("service reflection", "thrift service", __CLASS__."::".__METHOD__);
        }

        $methodGenerator = $this->getMethodGenerator();
        $methods         = array();
        /** @var MethodPrototype $methodPrototype */
        foreach ($this->serviceReflection->getMethodPrototypes() as $methodPrototype) {
            $methodGenerator->setMethodPrototype($methodPrototype);
            $methods[] = $this->getIndentation().$methodGenerator->generate();
        }
        $methods = implode(",\n", $methods);

        $name = $this->transformName($this->serviceReflection->getName());

        $search  = array("<name>", "<methods>");
        $replace = array($name, $methods);
        $service = str_replace($search, $replace, $this->getServiceTemplate());

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
        $typeTransformer = new TypeTransformer(
            new ComplexTypeNameTransformer($this->serviceReflection->getNamespaceName())
        );
        $generator = new MethodGenerator();
        $generator
            ->setTypeTransformer($typeTransformer)
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
}
