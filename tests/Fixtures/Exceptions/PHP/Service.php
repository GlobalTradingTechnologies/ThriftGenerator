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

namespace Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP;

/**
 * Test class to test exceptions support. All folded complex types (from exceptions and other complex types from
 * parameters and return values) should be collected as a structs
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class Service
{
    /**
     * throwsException description
     *
     * @param int $int integer param
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\DTO\DTO1 $complex complex param
     *
     * @throws \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\Exception\TestException in case of some failure
     *
     * @return \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\DTO\DTO1[] result
     */
    public function throwsException($int, $test)
    {
        // some logic here
    }
}
 