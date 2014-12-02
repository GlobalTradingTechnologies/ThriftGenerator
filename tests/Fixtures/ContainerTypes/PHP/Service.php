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

namespace Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\PHP;

/**
 * Test class to test container types support
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class Service
{
    /**
     * receivesListOfDTOsAndReturnsListOfDTOs description
     *
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\PHP\DTO\DTO1[] $listOfDTOs list of DTOs
     *
     * @return \Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\PHP\DTO\DTO1[] result
     */
    public function receivesListOfDTOsAndReturnsListOfDTOs($listOfDTOs)
    {
        // some logic here
    }

    /**
     * receiveArrayReturnsArrayWithNativeAnnotations description
     *
     * @param array $listOfDTOs list of DTOs
     *
     * @return array result
     */
    public function receiveArrayReturnsArrayWithNativeAnnotations(array $array = array())
    {
        // some logic here
    }

    /**
     * receiveArrayReturnsArrayWithExplicitAnnotations description
     *
     * @param string[] $listOfDTOs list of DTOs
     *
     * @return int[] result
     */
    public function receiveArrayReturnsArrayWithExplicitAnnotations(array $strings)
    {
        // some logic here
    }
}
 