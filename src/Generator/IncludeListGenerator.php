<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 11/25/14
 */

namespace Gtt\ThriftGenerator\Generator;

use Gtt\ThriftGenerator\Exception\InvalidArgumentException;
use Gtt\ThriftGenerator\Reflection\ComplexTypeReflection;
use Gtt\ThriftGenerator\Reflection\ServiceReflection;

/**
 * Generates include list
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class IncludeListGenerator extends AbstractGenerator
{
    /**
     * List of namespaces used in thrift definition that holds current include list
     *
     * @var string[]
     */
    protected $namespacesUsed = array();

    /**
     * List of namespaces that should be excluded from include generation process
     * This feature is helpful when there is a need to set namespaces covered inside
     * current thrift definition file
     *
     * @var array
     */
    protected $excludedNamespaces = array();

    /**
     * Sets excluded namespaces
     *
     * @param array $namespaces list of namespaces
     *
     * @return $this
     */
    public function setExcludedNamespaces(array $namespaces = array())
    {
        $this->excludedNamespaces = $namespaces;

        return $this;
    }

    /**
     * Sets used namespaces
     *
     * @param array $namespaces list of namespaces
     *
     * @return $this
     */
    public function setUsedNamespaces(array $namespaces = array())
    {
        $this->namespacesUsed = array_unique($namespaces);

        return $this;
    }

    /**
     * Collects list of used namespaces from service reflection
     *
     * @param ServiceReflection $serviceReflection service reflection
     *
     * @return $this
     */
    public function setUsedNamespacesFromServiceReflection(ServiceReflection $serviceReflection)
    {
        $complexTypes = $serviceReflection->getStructs() + $serviceReflection->getExceptions();

        $namespacesUsed = array();
        foreach ($complexTypes as $complexType) {
            $namespacesUsed[] = $complexType->getNamespaceName();
        }
        $this->setUsedNamespaces($namespacesUsed);

        return $this;
    }

    /**
     * Collects list of used namespaces from list of complex types
     *
     * @param ComplexTypeReflection[] $complexTypes list of complex types
     *
     * @return $this
     */
    public function setUsedNamespacesFromComplexTypes(array $complexTypes = array())
    {
        $namespacesUsed = array();
        foreach ($complexTypes as $complexType) {
            if (!$complexType instanceof ComplexTypeReflection) {
                throw new InvalidArgumentException("Include list can be generated using only ComplexTypeReflection instances");
            }
            foreach ($complexType->getPropertyDependencies() as $dependency) {
                $namespacesUsed[] = $dependency->getNamespaceName();
            }
        }
        $this->setUsedNamespaces($namespacesUsed);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $includeGenerator = $this->getIncludeGenerator();

        $includes = array();
        $targetNamespaces = array_diff($this->namespacesUsed, $this->excludedNamespaces);
        foreach ($targetNamespaces as $targetNamespace) {
            $includeGenerator->setNamespace($targetNamespace);
            $includes[] = $includeGenerator->generate();
        }

        $includes = implode("\n", $includes);
        return $includes;
    }

    /**
     * Creates and returns include generator
     *
     * @return IncludeGenerator
     */
    protected function getIncludeGenerator()
    {
        $generator = new IncludeGenerator();
        $generator->setIndentation($this->getIndentation());

        return $generator;
    }
}
