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

use Gtt\ThriftGenerator\Exception\DumpException;

/**
 * Dumps complex type definitions grouped by namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class NamespacedComplexTypeDumper extends AbstractFilesystemDumper
{
    /**
     * Namespace of complex types definition need to be dumped
     *
     * @var
     */
    protected $namespace;

    /**
     * Complex types definition
     *
     * @var string
     */
    protected $complexTypesDefinition;

    /**
     * Sets namespace of complex types need to be dumped
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
     * Sets complex types definition
     *
     * @param string $definition definition
     *
     * @return $this
     */
    public function setComplexTypesDefinition($definition)
    {
        $this->complexTypesDefinition = $definition;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        if (is_null($this->namespace)) {
            throw new DumpException("Complex types namespace is not specified");
        }
        if (is_null($this->outputDir)) {
            throw new DumpException("Output dir is not specified");
        }
        if (is_null($this->complexTypesDefinition)) {
            throw new DumpException("Complex types definition content is not specified");
        }

        $complexTypesPath = str_replace("\\", ".", ltrim($this->namespace, "\\"));
        $complexTypesPath = $this->outputDir.DIRECTORY_SEPARATOR.$complexTypesPath.".thrift";

        $this->dumpFile($complexTypesPath, $this->complexTypesDefinition);
    }
}
