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

namespace Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\Exception;

/**
 * Test class to test exceptions support
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class TestException extends \Exception
{
    /**
     * First property with string type with default integer value
     *
     * @var int
     */
    public $one = 1;

    /**
     * Second property with folded complex type without default value
     *
     * @var \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\DTO\DTO1
     */
    public $two;

    /**
     * Third property with complex type with default null value
     *
     * @var \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\PHP\DTO\DTO2[]
     */
    public $three = null;

    /**
     * Protected parameter
     *
     * @var int
     */
    protected $four;

    /**
     * Private parameter
     *
     * @var int
     */
    private $five;
}
