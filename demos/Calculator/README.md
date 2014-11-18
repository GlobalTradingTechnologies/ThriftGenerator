## Calculator demo

This demo allows you to play with original apache thrift bundled [example](http://thrift.apache.org/tutorial/php) with server that performs base Calculator operations (addition, subtraction, multiplication, division) and client that remotely calls such server.
In original thrift's example we should execute thrift tool to generate service classes using predefined *thrift-file* in order to manipulate with them during RPC-interaction.

Calculator demo makes possible to look at this example from another point of view.
Here it is a Calculator server class stored in [Source](Source) folder that is pretty similar to original CalculatorHandler class. Source directory also contains [Work](Source/Struct/Work.php) structure and [InvalidOperation](Source/Exception/InvalidOperation.php) exception as separated PHP classes. All the classes inside Source directory are defined with doc blocks specifying all the method parameters, returning types and exceptions throwing. Using this information we can automatically generate thrift file defines server Calculator internals with the help of ThriftGenerator. Using this thrift file all the service classes could be generated as well with thrift util in the same way as in original thrift example. Then it is possible to check validness of generated thrift file by processing common client-server interaction

### Execution

To execute the demo you need to have thrift tool installed (see instructions [here](http://thrift.apache.org/docs/BuildingFromSource)) to do three simple steps:

* Clone repository and go to [Calculator demo folder](../Calculator):
```
git clone https://github.com/GlobalTradingTechnologies/ThriftGenerator && cd ThriftGenerator/demos/Calculator
```

* Run local php server on 8080 port pointing to Server folder as a document root in order to make possible http client-server interaction:
```
php -S localhost:8080 -t ./Server
```
(This step is for PHP 5.4+ users. If you have PHP 5.3, please configure your web server manually instead)

* Run demo file
```
php ./demo.php
```
Once you run the demo thrift file would be generated using ThriftGenerator, than thrift service classes would be generated with the help of thrift tool. All the generated stuff can be found in [Generated](Generated) folder. Then base client-server interaction would be invoked. If all is well, something like this would appear:

```
Thrift definition file is generated using ThriftGenerator in <ThriftGenerator path>/demos/Calculator/Generated/generated.thrift
Service classes was generated using `thrift` tool in <ThriftGenerator path>/demos/Calculator/Generated/gen-php folder

Starting generated code execution ...

ping()
1+1=2
InvalidOperation: Cannot divide by 0
15-10=5
```