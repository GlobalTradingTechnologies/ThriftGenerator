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

use Gtt\ThriftGenerator\Exception\TargetNotSpecifiedException;

/**
 * Generates include node according to current thrift definition namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class IncludeGenerator extends AbstractGenerator
{
    /**
     * Current thrift definition namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Sets current thrift definition namespace
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
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->namespace)) {
            throw new TargetNotSpecifiedException("Corresponding PHP namespace", "include", __CLASS__."::".__METHOD__);
        }

        $include = str_replace("\\", ".", ltrim($this->namespace, "\\"));

        $include = str_replace("<include>", $include, $this->getIncludeTemplate());

        return $include;
    }

    /**
     * Returns include template
     *
     * @return string
     */
    protected function getIncludeTemplate()
    {
        $includeTemplate = <<<EOT
include "<include>.thrift"
EOT;
        return $includeTemplate;
    }
}
