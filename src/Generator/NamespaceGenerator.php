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

use Gtt\ThriftGenerator\Exception\TargetNotSpecifiedException;
use Gtt\ThriftGenerator\Exception\TransformerNotSpecifiedException;
use Gtt\ThriftGenerator\Transformer\TransformerInterface;

/**
 * Generates thrift namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class NamespaceGenerator extends AbstractGenerator
{
    /**
     * Namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Transformer for namespace name
     *
     * @var TransformerInterface
     */
    protected $namespaceTransformer;

    /**
     * Sets target namespace
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
        if (is_null($this->namespace)) {
            throw new TargetNotSpecifiedException("namespace", "namespace", __CLASS__."::".__METHOD__);
        }

        $namespace = $this->transformNamespace($this->namespace);

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
