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

namespace Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO;

/**
 * Data transfer object used to test container types support
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class DTO2
{
    /**
     * First property with folded complex type
     *
     * @var \Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO2[]
     */
    public $one;
}