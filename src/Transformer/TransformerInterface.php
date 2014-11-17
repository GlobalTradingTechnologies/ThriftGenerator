<?php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/14/14
 */

namespace Gtt\ThriftGenerator\Transformer;

/**
 * Defines base interface for transformers
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
interface TransformerInterface
{
    /**
     * Transforms some php entity to thrift entity
     *
     * @param string $entity PHP entity
     *
     * @return string thrift entity
     */
    public function transform($entity);
}
