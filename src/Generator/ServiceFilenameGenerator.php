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
 * Generates relative file path for the thrift definition of the single service
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceFilenameGenerator implements GeneratorInterface
{
    /**
     * Thrift file extension
     */
    const THRIFT_EXTENSION = "thrift";

    /**
     * Service's FQCN
     *
     * @var string
     */
    protected $serviceName;

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
     * {@inheritdoc}
     */
    public function generate()
    {
        $serviceFilename = str_replace("\\", ".", ltrim($this->serviceName, "\\")).".".static::THRIFT_EXTENSION;

        return $serviceFilename;
    }
}
