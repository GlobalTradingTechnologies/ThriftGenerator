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

use Gtt\ThriftGenerator\Exception\MethodNotSpecifiedException;
use Gtt\ThriftGenerator\Reflection\MethodPrototype;
use Gtt\ThriftGenerator\TypeHandler;

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
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->methodPrototype)) {
            throw new MethodNotSpecifiedException("Method to be handled is not specified");
        }

        $returnType = TypeHandler::transform($this->methodPrototype->getReturnType());
        $methodName = $this->methodPrototype->getMethodReflection()->getName();

        // parameters
        $parameters = array();
        $identifier = 0;
        /** @var ZendReflectionParameter $parameterRef */
        foreach ($this->methodPrototype->getParameters() as $parameterRef) {
            $identifier   += 1;
            $parameterType = TypeHandler::transform($parameterRef->getType());
            $parameterName = $parameterRef->getName();
            $parameters[]  = "$identifier:$parameterType $parameterName";
        }
        $parameters = "(".implode(", ", $parameters).")";

        //exceptions
        $exceptionRefs = $this->methodPrototype->getExceptions();
        $exceptions    = "";
        if ($exceptionRefs) {
            $identifier = 0;
            $exceptions = array();
            foreach ($exceptionRefs as $exceptionRef) {
                $identifier   += 1;
                $exceptionType = TypeHandler::transform($exceptionRef->getName());
                $exceptionName = $this->generateExceptionName(
                    $this->methodPrototype->getMethodReflection()->getDeclaringClass()->getName(),
                    $this->methodPrototype->getMethodReflection()->getName(),
                    $exceptionType
                );
                $exceptions[] = "$identifier:$exceptionType $exceptionName";
            }
            $exceptions = " throws (".implode(", ", $exceptions).")";
        }

        $search  = array("<return>", "<name>", "<parameters>", "<exceptions>");
        $replace = array($returnType, $methodName, $parameters, $exceptions);
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
     * Generates exception name
     * TODO rethink that (what name for exceptions in methods should be?)
     *
     * @param string $className class name
     * @param string $methodName method name
     * @param string $exceptionName exception name
     *
     * @return string
     */
    protected function generateExceptionName($className, $methodName, $exceptionName)
    {
        return implode("_", array($className, $methodName, $exceptionName));
    }
}