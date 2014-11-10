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
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->classRef)) {
            throw new ClassNotSpecifiedException("Class to be handled is not specified");
        }

        $namespace = $this->classRef->getName();

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
}
