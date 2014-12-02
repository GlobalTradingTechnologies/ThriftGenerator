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

namespace Gtt\ThriftGenerator\Tests;

use Gtt\ThriftGenerator\Generator\ThriftGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use PHPUnit_Framework_TestCase;
use Zend\Code\Reflection\FileReflection;

/**
 * Integration test for generator
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ThriftGenerator
     */
    protected $generator;

    public function setUp()
    {
        $this->generator = new ThriftGenerator();
    }

    /**
     * @test
     * @dataProvider getIntegrationTest
     */
    public function testIntegration($classRefs, $expectThriftDir)
    {
        $outputDir = vfsStream::setup('root');
        $this->generator
            ->setOutputDir(vfsStream::url('root'))
            ->setClasses($classRefs);
        $this->generator->generate();

        $generatedStructure = vfsStream::inspect(new vfsStreamStructureVisitor(), $outputDir)->getStructure();
        $generatedStructure = $generatedStructure['root'];
        $expectedStructure  = $this->convertDirectoryToArray($expectThriftDir);

        // sort structures
        $this->ksortRecursive($generatedStructure);
        $this->ksortRecursive($expectedStructure);

        $this->assertEquals($expectedStructure, $generatedStructure);
    }

    /**
     * Provides arrays with reflection classes and directory with expected thrift definitions
     *
     * @return array
     */
    public function getIntegrationTest()
    {
        $fixturesDir = realpath(__DIR__."/Fixtures");

        $testData = array();
        foreach (new \DirectoryIterator($fixturesDir) as $directory) {
            if ($directory->isDir() && !$directory->isDot()) {
                $testDirectoryPath = $directory->getPathname();

                $classesDir = $testDirectoryPath.DIRECTORY_SEPARATOR."PHP";
                $thriftDir  = $testDirectoryPath.DIRECTORY_SEPARATOR."thrift";

                $serviceClassesRefs = array();
                if (is_dir($classesDir) && is_dir($thriftDir)) {
                    // test case folder is valid
                    // recursively iterate through classesDir in order to collect 'services' (classes with names starting with 'Service')
                    $classesDirIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($classesDir));
                    $regexIterator = new \RegexIterator($classesDirIterator, '/^.+\/Service[^\/]*\.php$/i', \RecursiveRegexIterator::GET_MATCH);
                    foreach($regexIterator as $service) {
                        // fetch service reflection
                        $serviceFileZendReflection = new FileReflection($service[0], true);
                        $serviceClassesRefs[]      = $serviceFileZendReflection->getClass();
                    }
                    $testData[] = array($serviceClassesRefs, $thriftDir);
                }
            }
        }

        return $testData;
    }

    /**
     * Converts filesystem directory to array
     *
     * @param string $dir path to dir
     *
     * @return array
     */
    protected function convertDirectoryToArray($dir)
    {
        $array       = array();
        $dirIterator = new \DirectoryIterator($dir);

        foreach ($dirIterator as $node) {
            if (!$node->isDot()) {
                if ($node->isDir()) {
                    $array[$node->getFilename()] = $this->convertDirectoryToArray($node->getPathname());
                } elseif ($node->isFile()) {
                    $array[$node->getFilename()] = file_get_contents($node->getPathname());
                }
            }
        }

        return $array;
    }

    /**
     * Recursive ksort
     *
     * @param $array
     *
     * @return bool
     */
    protected function ksortRecursive($array)
    {
        if (!is_array($array)) {
            return false;
        }
        ksort($array);
        foreach ($array as &$arr) {
            $this->ksortRecursive($arr);
        }
        return true;
    }
}
 