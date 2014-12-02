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
use Gtt\ThriftGenerator\Reflection\MethodPrototype;
use Gtt\ThriftGenerator\Transformer\TransformerInterface;

use Zend\Server\Reflection\ReflectionParameter as ZendReflectionParameter;

/**
 * Generates thrift service method
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class MethodGenerator extends AbstractGenerator
{
    /**
     * Method prototype
     *
     * @var MethodPrototype
     */
    protected $methodPrototype;

    /**
     * Type transfer
     *
     * @var TransformerInterface
     */
    protected $typeTransformer;

    /**
     * Sets method prototype
     *
     * @param MethodPrototype $methodPrototype method prototype
     *
     * @return $this
     */
    public function setMethodPrototype(MethodPrototype $methodPrototype)
    {
        $this->methodPrototype = $methodPrototype;
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
        if (is_null($this->methodPrototype)) {
            throw new TargetNotSpecifiedException("method prototype reflection", "method", __CLASS__."::".__METHOD__);
        }

        $return     = $this->generateReturn();
        $name       = $this->generateMethodName();
        $parameters = $this->generateParameters();
        $exceptions = $this->generateExceptions();

        $search  = array("<return>", "<name>", "<parameters>", "<exceptions>");
        $replace = array($return, $name, $parameters, $exceptions);
        $method  = str_replace($search, $replace, $this->getMethodTemplate());

        return $method;
    }

    /**
     * Returns method template
     *
     * @return string
     */
    protected function getMethodTemplate()
    {
        $methodTemplate = <<<EOT
<return> <name><parameters><exceptions>
EOT;
        return $methodTemplate;
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
     * Generates exception name
     * TODO rethink that (what name for exceptions in methods should be?)
     *
     * @param \ReflectionClass $exceptionRef class name
     *
     * @return string
     */
    protected function generateExceptionName(\ReflectionClass $exceptionRef)
    {
        return $exceptionRef->getShortName();
    }

    /**
     * Generates return statement
     *
     * @return string
     */
    protected function generateReturn()
    {
        $return = $this->transformType($this->methodPrototype->getReturnType());

        return $return;
    }

    /**
     * Generates method name
     *
     * @return string
     */
    protected function generateMethodName()
    {
        $methodName = $this->methodPrototype->getMethodReflection()->getName();

        return $methodName;
    }

    /**
     * Generates method parameters
     *
     * @return string
     */
    protected function generateParameters()
    {
        $parameters = array();
        $identifier = 0;
        /** @var ZendReflectionParameter $parameterRef */
        foreach ($this->methodPrototype->getParameters() as $parameterRef) {
            $identifier   += 1;
            $parameterType = $this->transformType($parameterRef->getType());
            $parameterName = $parameterRef->getName();
            $parameters[]  = "$identifier:$parameterType $parameterName";
        }
        $parameters = "(" . implode(", ", $parameters) . ")";

        return $parameters;
    }

    /**
     * Generates exceptions can be thrown inside the method
     *
     * @return string
     */
    protected function generateExceptions()
    {
        $exceptionRefs = $this->methodPrototype->getExceptions();

        $exceptions = "";
        if ($exceptionRefs) {
            $identifier = 0;
            $exceptions = array();
            foreach ($exceptionRefs as $exceptionRef) {
                $identifier   += 1;
                $exceptionType = $this->transformType($exceptionRef->getName());
                $exceptionName = $this->generateExceptionName($exceptionRef);
                $exceptions[]  = "$identifier:$exceptionType $exceptionName";
            }
            $exceptions = " throws (" . implode(", ", $exceptions) . ")";
            return $exceptions;
        }

        return $exceptions;
    }
}