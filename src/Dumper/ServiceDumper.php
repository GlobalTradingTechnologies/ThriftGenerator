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
 * Dumps generated service definition
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceDumper extends AbstractFilesystemDumper
{
    /**
     * Service's FQCN
     *
     * @var string
     */
    protected $serviceName;

    /**
     * Service definition to be dumped
     *
     * @var string
     */
    protected $serviceDefinition;

    /**
     * Sets service class name
     *
     * @param string $serviceName service FQCN
     *
     * @return $this
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Sets service definition need to be dumped
     *
     * @param string $serviceDefinition service definition
     *
     * @return $this
     */
    public function setServiceDefinition($serviceDefinition)
    {
        $this->serviceDefinition = $serviceDefinition;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        if (is_null($this->serviceName)) {
            throw new DumpException("Service full class name is not specified");
        }
        if (is_null($this->outputDir)) {
            throw new DumpException("Output dir is not specified");
        }
        if (is_null($this->serviceDefinition)) {
            throw new DumpException("Service definition content is not specified");
        }

        $servicePath = str_replace("\\", ".", $this->serviceName);
        $servicePath = $this->outputDir.DIRECTORY_SEPARATOR.$servicePath.".thrift";

        $this->dumpFile($servicePath, $this->serviceDefinition);
    }
}
