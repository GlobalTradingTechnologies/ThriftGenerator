<?php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/17/14
 */

namespace Gtt\ThriftGenerator\Transformer;

/**
 * Transforms fully qualified PHP class to short thrift service name
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceNameTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($entity)
    {
        $entityClass = new \ReflectionClass($entity);

        return $entityClass->getShortName();
    }
}
