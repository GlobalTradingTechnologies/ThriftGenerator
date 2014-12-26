<?php
//#!/usr/bin/env php
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
//include __DIR__ . '/../../autoload.php';
// there is no introspection for enums for now - so we can not use them on client-side
// use Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Operation;


// thrift autoload from gen-php folder
$thriftLibDir = __DIR__."/../../../vendor/apache/thrift";
require_once $thriftLibDir.'/lib/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Service\CalculatorClient;
use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Work;
use Demo\Generated\Gtt\ThriftGenerator\Demo\Calculator\Source\Exception\InvalidOperation;

$GEN_DIR = realpath(dirname(__FILE__).'/../Generated/PHP');
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

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

try {
  $socket = new THttpClient('localhost', 8080, '/Server.php');
  $rawTransport = $socket;

  $transport = new TBufferedTransport($rawTransport, 1024, 1024);
  $protocol = new TBinaryProtocol($transport);
  $client = new CalculatorClient($protocol);

  $transport->open();

  $client->ping();
  print "ping()\n";

  $sum = $client->add(1,1);
  print "1+1=$sum\n";

  $work = new Work();

  // there is no introspection for enums for now - so we can not use them on client-side
  // $work->op = Operation::DIVIDE;
  $work->op = 4;
  $work->num1 = 1;
  $work->num2 = 0;

  try {
    $client->calculate(1, $work);
    print "Whoa! We can divide by zero?\n";
  } catch (InvalidOperation $io) {
    print "InvalidOperation: $io->why\n";
  }

  // there is no introspection for enums for now - so we can not use them on client-side
  // $work->op = Operation::SUBTRACT;
  $work->op = 2;
  $work->num1 = 15;
  $work->num2 = 10;
  $diff = $client->calculate(1, $work);
  print "15-10=$diff\n";

  $transport->close();

} catch (TException $tx) {
  print 'TException: '.$tx->getMessage()."\n";
}

?>