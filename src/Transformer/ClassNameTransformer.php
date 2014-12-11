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
 * Transforms PHP's FQCN into thrift struct/exception name
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ClassNameTransformer implements TransformerInterface
{
    /**
     * Holds current thrift file namespace used to determine
     * which entities are defined inside it's own namespace and
     * which are imported from another namespace
     *
     * @var string
     */
    protected $currentNamespace;

    /**
     * {@inheritdoc}
     */
    public function transform($entity)
    {
        $complexTypeNameRef = new \ReflectionClass($entity);
        if ($this->currentNamespace == $complexTypeNameRef->getNamespaceName()) {
            // entity defined inside of it's own namespace
            $complexTypeName = $complexTypeNameRef->getShortName();
        } else {
            // entity is imported from another namespace
            $complexTypeName = str_replace("\\", ".", ltrim($entity, "\\"));
        }

        return $complexTypeName;
    }

    /**
     * Sets current thrift file namespace
     *
     * @param string $namespace namespace
     *
     * @return $this
     */
    public function setCurrentNamespace($namespace)
    {
        $this->currentNamespace = ltrim($namespace, "\\");

        return $this;
    }

    /**
     * Returns current thrift file namespace
     *
     * @return string
     */
    public function getCurrentNamespace()
    {
        return $this->currentNamespace;
    }
}
