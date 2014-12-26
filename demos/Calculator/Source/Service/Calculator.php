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

namespace Gtt\ThriftGenerator\Demo\Calculator\Source\Service;

use Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Work;
use Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Operation;
use Gtt\ThriftGenerator\Demo\Calculator\Source\Exception\InvalidOperation;

/**
 * Calculator class that is similar to thrift tutorial
 * (without logging since PHP does not support this features, SharedStruct handling and zip method)
 * and described using annotations
 */
class Calculator
{
    /**
     * Pings the service
     */
    public function ping()
    {
        error_log("ping()");
    }

    /**
     * Adds one number to another
     *
     * @param int $num1 first number
     * @param int $num2 second number
     *
     * @return int
     */
    public function add($num1, $num2)
    {
        error_log("add({$num1}, {$num2})");
        return $num1 + $num2;
    }

    /**
     * Calculates something
     *
     * @param int $logid log id
     * @param \Gtt\ThriftGenerator\Demo\Calculator\Source\Struct\Work $w contains operation description
     *
     * @throws \Gtt\ThriftGenerator\Demo\Calculator\Source\Exception\InvalidOperation in case of invalid operations
     *
     * @return float
     */
    public function calculate($logid, Work $w)
    {
        error_log("calculate({$logid}, {{$w->op}, {$w->num1}, {$w->num2}})");
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
}
