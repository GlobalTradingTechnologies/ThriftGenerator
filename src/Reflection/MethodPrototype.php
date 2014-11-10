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

use Gtt\ThriftGenerator\Exception\InvalidArgumentException;

use Zend\Server\Reflection\Prototype;
use Zend\Server\Reflection\ReflectionParameter;
use Zend\Server\Reflection\ReflectionReturnValue;

use ReflectionClass;
use ReflectionMethod;

/**
 * Method prototype. Extends the base zend prototype with
 * with method extensions handling and filling prototype with original
 * method reflection
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class MethodPrototype extends Prototype
{
    /**
     * List of exception reflections
     *
     * @var ReflectionClass[]
     */
    protected $exceptions;

    /**
     * Target method reflection
     *
     * @var ReflectionMethod
     */
    protected $methodRef;

    /**
     * Constructor
     *
     * @param ReflectionMethod $methodRef original method reflection
     * @param ReflectionReturnValue $return return value
     * @param ReflectionParameter[] $params params
     * @param ReflectionClass[] $exceptions exceptions
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ReflectionMethod $methodRef,
        ReflectionReturnValue $return,
        array $params = array(),
        array $exceptions = array())
    {
        foreach ($exceptions as $exception) {
            if (!$exception instanceof ReflectionClass) {
                throw new InvalidArgumentException('One or more exceptions are invalid');
            }
        }
        $this->exceptions = $exceptions;
        $this->methodRef  = $methodRef;
        parent::__construct($return, $params);
    }

    /**
     * Returns original method reflection
     *
     * @return ReflectionMethod
     */
    public function getMethodReflection()
    {
        return $this->methodRef;
    }

    /**
     * Returns method exceptions
     *
     * @return ReflectionClass[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
}
