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

/**
 * Generator interface
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
interface GeneratorInterface
{
    /**
     * Generates the stuff
     *
     * @return string
     */
    public function generate();
}
