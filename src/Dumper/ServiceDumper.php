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

use Gtt\ThriftGenerator\Dumper\Exception\DumpException;
use Gtt\ThriftGenerator\Generator\ServiceFilenameGenerator;

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
     * Returns service class name
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
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
     * Returns service definition need to be dumped
     *
     * @return string
     */
    public function getServiceDefinition()
    {
        return $this->serviceDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        if (is_null($this->serviceName)) {
            throw new DumpException("Service full class name is not specified");
        }
        if (is_null($this->outputDir) || !is_dir($this->outputDir) || !is_writable($this->outputDir)) {
            throw new DumpException("Output dir is not specified, not exists or not writable");
        }
        if (is_null($this->serviceDefinition)) {
            throw new DumpException("Service definition content is not specified");
        }

        $filenameGenerator = $this->getFilenameGenerator();
        $servicePath = $this->outputDir.DIRECTORY_SEPARATOR.$filenameGenerator->generate();

        $this->dumpFile($servicePath, $this->serviceDefinition);
    }

    /**
     * Creates and returns service filename generator
     *
     * @return ServiceFilenameGenerator
     */
    protected function getFilenameGenerator()
    {
        $filenameGenerator = new ServiceFilenameGenerator();
        $filenameGenerator->setServiceName($this->serviceName);

        return $filenameGenerator;
    }
}
