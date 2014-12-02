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

namespace Gtt\ThriftGenerator\Tests\Fixtures\Structs\PHP\DTO;

/**
 * Data transfer object used to test thrift service structs support
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class DTO2
{
    /**
     * First parameter with string type with default string value
     *
     * @var string
     */
    public $one = "one default";

    /**
     * Second parameter with int (written as 'integer') type with default value
     *
     * @var integer
     */
    public $two = 123;

    /**
     * Third parameter with folded complex type without default value
     *
     * @var \Gtt\ThriftGenerator\Tests\Fixtures\Structs\PHP\DTO\DTO2
     */
    public $three;

    /**
     * Protected parameter
     *
     * @var int
     */
    protected $four;
}