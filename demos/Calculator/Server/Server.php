<?php
// #!/usr/bin/env php
/**
 * This file is part of the Global Trading Technologies Ltd package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 * 
 * Date: 11/13/14
 */

error_reporting(E_ALL);

// composer autoload
include __DIR__ . '/../../autoload.php';

use Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Operation;


// thrift autoload from gen-php folder
$thriftLibDir = __DIR__."/../../../vendor/apache/thrift";
$GEN_DIR      = realpath(dirname(__FILE__).'/../Generated/PHP');
require_once $thriftLibDir.'/lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Work;
use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Exception\InvalidOperation;

use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Service\CalculatorProcessor;
use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Service\CalculatorIf;

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', $thriftLibDir.'/lib/php/lib');
$loader->registerDefinition('Demo\Generated', $GEN_DIR);
$loader->register();

/*
 * This code is based on original apache thrift's example
 * See original example at http://thrift.apache.org/tutorial/php
 */

/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

if (php_sapi_name() == 'cli') {
  ini_set("display_errors", "stderr");
}

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

class CalculatorHandler implements CalculatorIf {
  protected $log = array();

  public function ping() {
    error_log("ping()");
  }

  public function add($num1, $num2) {
    error_log("add({$num1}, {$num2})");
    return $num1 + $num2;
  }

  public function calculate($logid, Work $w) {
    error_log("calculate({$logid}, {$w->op}, {$w->num1}, {$w->num2}})");
    switch ($w->op) {
        case Operation::ADD:
            $val = $w->num1 + $w->num2;
            break;
        case Operation::SUBTRACT:
            $val = $w->num1 - $w->num2;
            break;
        case Operation::MULTIPLY:
            $val = $w->num1 * $w->num2;
            break;
        case Operation::DIVIDE:
            if ($w->num2 == 0) {
                $io = new InvalidOperation();
                $io->what = $w->op;
                $io->why = "Cannot divide by 0";
                throw $io;
            }
            $val = $w->num1 / $w->num2;
            break;
        default:
            $io = new InvalidOperation();
            $io->what = $w->op;
            $io->why = "Invalid Operation";
            throw $io;
    }

    return $val;
  }
};

header('Content-Type', 'application/x-thrift');
if (php_sapi_name() == 'cli') {
  echo "\r\n";
}

$handler = new CalculatorHandler();
$processor = new CalculatorProcessor($handler);

$rawTransport = new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W);

$transport = new TBufferedTransport($rawTransport);
$protocol = new TBinaryProtocol($transport, true, true);

$transport->open();
$processor->process($protocol, $protocol);
$transport->close();