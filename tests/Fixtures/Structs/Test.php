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

namespace Gtt\ThriftGenerator\Tests\Fixtures\Structs;

/**
 * Test class to test thrift structs support
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class Test
{
    /**
     * receivesDTOandReturnsDTO description
     *
     * @param int $int integer param
     * @param string $string string param
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 $dto2 $dto2
     *
     * @return \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO1 result
     */
    public function receivesDTOAndReturnsDTO($int, $string, \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 $dto2)
    {
        // some logic here
    }
}
 