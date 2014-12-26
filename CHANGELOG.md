### 0.3.0 (2014-12-26)

  * Cast PHP array to thrift maps (instead of lists)  by default
  * Move all files inside src folder into Gtt/ThriftGenerator subfolder in order to allow old-way psr-0 autoloading
  * Add ability to fetch all recursive dependencies of service (ServiceReflection::getTransitiveComplexTypes)
  * Change demos namespace to Gtt\ThriftGenerator\Demo

### 0.2.0 (2014-12-16)

  * Add support of generating thrift definitions for several classes
  * Grouping complex types and exception dependencies be it's namespaces
  * Add dumpers to flexibly organize dumping process
  * Divide exceptions by components

### 0.1.0 (2014-11-18)

  * Initial release