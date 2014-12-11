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

/**
 * Abstract filesystem dumper
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
abstract class AbstractFilesystemDumper implements DumperInterface
{
    /**
     * Directory used to write output thrift definition files
     *
     * @var string
     */
    protected $outputDir;

    /**
     * Flags defines whether existing files could be rewritten during dump or not
     *
     * @var bool
     */
    protected $overwriteExisting = true;

    /**
     * Sets output directory path
     *
     * @param string $dir path to output directory
     *
     * @return $this
     */
    public function setOutputDir($dir)
    {
        $this->outputDir = $dir;

        return $this;
    }

    /**
     * Returns output directory path
     *
     * @return string
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * Sets rewrite existing flag value
     *
     * @param bool $overwriteExisting
     */
    public function setOverwriteExisting($overwriteExisting = true)
    {
        $this->overwriteExisting = (bool)$overwriteExisting;
    }

    /**
     * Returns rewrite existing flag value
     *
     * @return bool
     */
    public function getOverwriteExisting()
    {
        return $this->overwriteExisting;
    }

    /**
     * Dumps thrift definition file into the filesystem
     *
     * @param string $path file path
     * @param string $content file content
     *
     * @throws DumpException in case of failure
     */
    protected function dumpFile($path, $content)
    {
        if (!$this->overwriteExisting && file_exists($path)) {
            throw new DumpException("Can not dump thrift definition into $path. File already exists");
        }
        if (file_put_contents($path, $content) === false) {
            throw new DumpException("Can not dump thrift definition into $path due to internal error");
        }
    }
}
