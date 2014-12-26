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

/**
 * Holds base generators functionality
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * Indentation
     *
     * @var string
     */
    protected $indentation = "    ";

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndentation()
    {
        return $this->indentation;
    }

    /**
     * Sets indentation
     *
     * @param string $indentation indentation
     *
     * @return $this
     */
    public function setIndentation($indentation)
    {
        $this->indentation = (string) $indentation;
        return $this;
    }
}
