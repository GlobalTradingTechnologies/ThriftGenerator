<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 12/1/14
 */

namespace Gtt\ThriftGenerator\Dumper;

/**
 * Thrift definitions dumper
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
interface DumperInterface
{
    /**
     * Dumps thrift definitions
     *
     * @return void
     */
    public function dump();
}
