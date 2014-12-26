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

namespace Gtt\ThriftGenerator\Demo\Calculator\Source\Struct;

/**
 * Operation enum
 */
final class Operation
{
    const ADD = 1;
    const SUBTRACT = 2;
    const MULTIPLY = 3;
    const DIVIDE = 4;
    static public $__names = array(
      1 => 'ADD',
      2 => 'SUBTRACT',
      3 => 'MULTIPLY',
      4 => 'DIVIDE',
    );
}
