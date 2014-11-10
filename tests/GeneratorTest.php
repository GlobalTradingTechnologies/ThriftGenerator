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

use PHPUnit_Framework_TestCase;

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
    public function testIntegration($classRef, $expectedThriftFile)
    {
        $this->generator->setClass($classRef);
        $generatedThriftContent = $this->generator->generate();
        $this->assertStringEqualsFile($expectedThriftFile, $generatedThriftContent);
    }

    /**
     * Traverses directories (not recursively) inside Fixtures folder
     * and looks for Test.php and thrift.thrift files inside such each directory.
     * Test.php - can contain class for service definition generation and thrift.thrift -
     * expected thrift definition for this class.
     * Test.php may contain classes in future when library would
     * support thrift file generation base on several input classes. Now it supports
     * thrift file generation based on one class
     *
     * @return array of arrays with reflection class and expected thrift file content
     */
    public function getIntegrationTest()
    {
        $fixturesDir = realpath(__DIR__."/Fixtures");

        $testData = array();
        foreach (new \DirectoryIterator($fixturesDir) as $directory) {
            if ($directory->isDir() && !$directory->isDot()) {
                $testDirectoryPath = $directory->getPathname();
                if (file_exists($expectedThriftFile = $testDirectoryPath.DIRECTORY_SEPARATOR."thrift.thrift") &&
                    file_exists($serviceFileName = $testDirectoryPath.DIRECTORY_SEPARATOR."Test.php")) {
                    $serviceClassName = implode(
                        "\\",
                        array(__NAMESPACE__, 'Fixtures', $directory->getFilename(), "Test")
                    );
                    $testData[] = array(new \ReflectionClass($serviceClassName), $expectedThriftFile);
                }
            }
        }

        return $testData;
    }
}
 