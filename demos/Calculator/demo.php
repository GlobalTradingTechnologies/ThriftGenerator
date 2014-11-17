<?php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/12/14
 */

namespace Gtt\ThriftGenerator\Example\Calculator;

use Gtt\ThriftGenerator\Generator\ThriftGenerator;

use Symfony\Component\Process\Process;

use ReflectionClass;


ini_set('display_errors', true);

include __DIR__ . '/../autoload.php';

$generatedFolder = __DIR__."/Generated";

// Check the requirements
checkEnv();

// Generate thrift file
try{
    $generator = new ThriftGenerator();
    $generator->setClass(new ReflectionClass("Gtt\ThriftGenerator\Example\Calculator\Source\Service\Calculator"));
    $thriftFilePath = $generatedFolder."/generated.thrift";
    file_put_contents($thriftFilePath, $generator->generate());
    echo "Thrift definition file is successfully generated using ThriftGenerator in $thriftFilePath\n";
} catch(\Exception $ex) {
    die("Something went wrong: ".$ex->getMessage());
}

// Generate thrift classes
$pb = new \Symfony\Component\Process\ProcessBuilder();
$process = $pb
    ->setPrefix("thrift")
    ->setArguments(array(
        "-o", $generatedFolder,
        "--gen", "php:server,oop,rest",
        $thriftFilePath
    ))
    ->getProcess();
$process->run();
if ($process->isSuccessful()) {
    echo "'thrift' tool was succeeded using $thriftFilePath file with generation classes inside $generatedFolder/gen-php folder\n\n";
}

// starting demo
echo "Starting generated code execution ...\n\n";
include __DIR__."/Client/Client.php";

/**
 * Checks base demo launch requirements
 */
function checkEnv()
{
    // Check thrift is installed
    $process = new Process('thrift --version');
    $process->run();
    // we cannot use isSuccessful method since thrift exit code is 1 for some regular commands like '--version'. Really weird
    if (strpos($process->getOutput(), "Thrift version") !== 0) {
        throw new \RuntimeException('thrift is required to launch the demo');
    }

    // Check server is available
    $resource  = @fsockopen('localhost', 8080, $errorCode, $errorMsg);
    if (!$resource) {
        throw new \RuntimeException("Connection to localhost:8080 cannot be established\n".
            "Please start web server listening 8080 port with document root located at ./Server folder\n"
        );
    }
}