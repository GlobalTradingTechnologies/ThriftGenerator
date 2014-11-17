<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 10/16/14
 */

namespace Gtt\ThriftGenerator\Generator;

use Gtt\ThriftGenerator\Exception\ClassNotSpecifiedException;
use Gtt\ThriftGenerator\Exception\TransformerNotSpecifiedException;
use Gtt\ThriftGenerator\Transformer\TransformerInterface;

use \ReflectionClass;

/**
 * Generates thrift namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class NamespaceGenerator extends AbstractGenerator
{
    /**
     * Class reflection
     *
     * @var ReflectionClass
     */
    protected $classRef;

    /**
     * Transformer for namespace name
     *
     * @var TransformerInterface
     */
    protected $namespaceTransformer;

    /**
     * Sets target class reflection
     *
     * @param ReflectionClass $classRef class reflection
     *
     * @return $this
     */
    public function setClass(ReflectionClass $classRef)
    {
        $this->classRef = $classRef;
        return $this;
    }

    /**
     * Sets namespace name transformer
     *
     * @param TransformerInterface $transformer namespace name transformer
     *
     * @return $this
     */
    public function setNamespaceTransformer(TransformerInterface $transformer)
    {
        $this->namespaceTransformer = $transformer;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->classRef)) {
            throw new ClassNotSpecifiedException("Class to be handled is not specified");
        }

        $namespace = $this->transformNamespace($this->classRef->getNamespaceName());

        return str_replace("<namespace>", $namespace, $this->getNamespaceTemplate());
    }

    /**
     * Returns namespace template
     *
     * @return string
     */
    protected function getNamespaceTemplate()
    {
        $namespaceTemplate = <<<EOT
namespace php <namespace>
EOT;
        return $namespaceTemplate;
    }

    /**
     * Transforms namespace
     *
     * @param string $namespace namespace
     *
     * @throws TransformerNotSpecifiedException is transformer is not specified
     *
     * @return string
     */
    protected function transformNamespace($namespace)
    {
        if (!$this->namespaceTransformer) {
            throw new TransformerNotSpecifiedException("Namespace", $namespace);
        }
        return $this->namespaceTransformer->transform($namespace);
    }
}
