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

use Gtt\ThriftGenerator\Exception\ServiceNotSpecifiedException;
use Gtt\ThriftGenerator\Reflection\ServiceReflection;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;

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
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->serviceReflection)) {
            throw new ServiceNotSpecifiedException("Service to be handled is not specified");
        }

        $methodGenerator = $this->getMethodGenerator();
        $methods         = array();
        /** @var MethodPrototype $methodPrototype */
        foreach ($this->serviceReflection->getMethodPrototypes() as $methodPrototype) {
            $methodGenerator->setMethodPrototype($methodPrototype);
            $methods[] = $this->getIndentation().$methodGenerator->generate();
        }
        $methods = implode(",\n", $methods);

        $name = $this->serviceReflection->getName();

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
        $generator = new MethodGenerator();
        $generator->setIndentation($this->getIndentation());
        return $generator;
    }
}
