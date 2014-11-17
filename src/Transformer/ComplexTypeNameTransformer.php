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
 * Transforms PHP fully qualified complex type (DTO's, exceptions) name into thrift struct/exception name
 * Uses namespace of the service that current complex type instance is attended to in order
 * to reduce complex type full name and make it unique but as short as possible.
 *
 * For example if service name is \Vendor\Test\SomeName\Service\TestService and
 * original complex type namespace is \Vendor\Test\AnotherName\DTO\TestDTO then
 * result thrift's complex type name would be AnotherNameDTOTestDTO
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ComplexTypeNameTransformer implements TransformerInterface
{
    /**
     * Namespace of the service that current complex type instance is attended to
     *
     * @var string
     */
    protected $serviceNamespace;

    /**
     * Constructor
     *
     * @param string $serviceNamespace namespace of the service that current complex type instance is attended to
     */
    public function __construct($serviceNamespace = '')
    {
        $this->serviceNamespace = $serviceNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($entity)
    {
        // reach complex type namespace
        $entityClass     = new \ReflectionClass($entity);
        $entityNamespace = $entityClass->getNamespaceName();

        $entityNamespaceTail = $this->reduceServiceNamespace($entityNamespace);
        $thriftName          = str_replace("\\", "", $entityNamespaceTail).$entityClass->getShortName();

        return $thriftName;
    }

    /**
     * Reduces entity namespace with service namespace
     *
     * @param string $entityNamespace complex type namespace
     *
     * @return string
     */
    protected function reduceServiceNamespace($entityNamespace)
    {
        // append the both of namespaces with trailing slash in order to handle cases when one namespace is a part of another
        $serviceNamespace = $this->serviceNamespace."\\";
        $entityNamespace  = $entityNamespace."\\";
        //get the min length of service namespace and entitiy's one
        $serviceNamespaceLength = strlen($serviceNamespace);
        $entityNamespaceLength  = strlen($entityNamespace);
        $minNamespaceLength     = ($serviceNamespaceLength <= $entityNamespaceLength) ? $serviceNamespaceLength : $entityNamespaceLength;

        $i = 0;
        $lastNamespaceSeparatorPos = 0;
        while ($i < $minNamespaceLength && (substr($this->serviceNamespace, 0, $i) == substr($entityNamespace, 0, $i))) {
            if ($entityNamespace[$i] == "\\") {
                $lastNamespaceSeparatorPos = $i;
            }
            $i++;
        }
        return substr($entityNamespace, $lastNamespaceSeparatorPos + 1);
    }
}
