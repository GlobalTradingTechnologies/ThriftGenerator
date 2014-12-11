<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 12/11/14
 */

namespace Gtt\ThriftGenerator\Generator;

/**
 * Generates relative file path for the thrift definition of the list of the complex types grouped by namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class NamespacedComplexTypeFilenameGenerator
{
    /**
     * Thrift file extension
     */
    const THRIFT_EXTENSION = "thrift";

    /**
     * Namespace of complex types definition
     *
     * @var string
     */
    protected $namespace;

    /**
     * Complex types definition
     *
     * @var string
     */
    protected $complexTypesDefinition;

    /**
     * Sets namespace of complex types
     *
     * @param string $namespace namespace
     *
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Returns namespace of complex types need to be dumped
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $complexTypesFilename = str_replace("\\", ".", ltrim($this->namespace, "\\")).".".static::THRIFT_EXTENSION;

        return $complexTypesFilename;
    }
}
