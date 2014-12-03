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
use Symfony\Component\Process\ProcessBuilder;

use ReflectionClass;


ini_set('display_errors', true);

include __DIR__ . '/../autoload.php';

$generatedFolder = __DIR__."/Generated";
$thriftGeneratedFolder = $generatedFolder."/thrift";
$phpGeneratedFolder = $generatedFolder."/PHP";

// Check the requirements
checkEnv($thriftGeneratedFolder, $phpGeneratedFolder);

// Generate thrift definition files
mkdir($thriftGeneratedFolder);
try{
    $className = "Gtt\ThriftGenerator\Example\Calculator\Source\Service\Calculator";

    $generator = new ThriftGenerator();
    $generator
        ->setClasses(array(new ReflectionClass($className)))
        ->setOutputDir($thriftGeneratedFolder);
    $generator->generate();
    echo "Thrift definition files are generated using ThriftGenerator in $generatedFolder\n";
} catch(\Exception $ex) {
    die("Can not generate thrift definition files: ".$ex->getMessage());
}

// Generate thrift classes
mkdir($phpGeneratedFolder);
$serviceName        = str_replace("\\", ".", $className);
$serviceFileName    = $thriftGeneratedFolder."/".$serviceName.".thrift";
$pb = new ProcessBuilder();
$process = $pb
    ->setPrefix("thrift")
    ->setArguments(array(
        "-r",
        "--out", $phpGeneratedFolder,
        "--gen", "php:server,oop,nsglobal=Demo\\Generated",
        $serviceFileName
    ))
    ->getProcess();
$process->run();
if ($process->isSuccessful()) {
    echo "Service classes was generated using `thrift` compiler in $generatedFolder folder\n\n";
} else {
    die("Can not compile thrift service classes: ".$process->getErrorOutput());
}

// starting demo
echo "Starting generated code execution ...\n\n";
include __DIR__."/Client/Client.php";

/**
 * Checks base demo launch requirements
 *
 * @param string $thriftGeneratedFolder folder for generated thrift files (by ThriftGenerator). Should not exist before demo launch
 * @param string $phpGeneratedFolder folder for generated PHP code (by thrift compiler). Should not exist before demo launch
 */
function checkEnv($thriftGeneratedFolder, $phpGeneratedFolder)
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

    if (is_dir($thriftGeneratedFolder)) {
        throw new \RuntimeException("$thriftGeneratedFolder should not exist to launch thrift definition generation. ".
            "Consider to remove it if you perform repeated demo launch");
    }

    if (is_dir($phpGeneratedFolder)) {
        throw new \RuntimeException("$phpGeneratedFolder should not exist to launch thrift compilation. ".
            "Consider to remove it if you perform repeated demo launch");
    }
}