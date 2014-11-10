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

namespace Gtt\ThriftGenerator\Tests\Fixtures\Basics;

/**
 * Test class to test the thrift file generations basics
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class Test
{
    /**
     * returnsString Description
     *
     * @param int $int integer param
     * @param string $string string param
     *
     * @return string
     */
    public function returnsString($int, $string)
    {
        // some logic here
    }

    /**
     * returnsNothing description
     *
     * @param int $int integer param
     * @param string $string string param
     */
    public function returnsNothing($int, $string)
    {
        // some logic here
    }

    /**
     * returnsNothingWithVoidAnnotation description
     *
     * @param int $int integer param
     * @param string $string string param
     *
     * @return void
     */
    public function returnsNothingWithVoidAnnotation($int, $string)
    {
        // some logic here
    }

    /**
     * staticMethod description
     *
     * @return string test
     */
    public static function staticMethod()
    {
        // some logic here
    }

    /**
     * This shouldn't be in result thrift
     */
    protected function protectedMethod()
    {
        // some logic here
    }

    /**
     * This shouldn't be in result thrift
     */
    protected function privateMethod()
    {
        // some logic here
    }
}
 