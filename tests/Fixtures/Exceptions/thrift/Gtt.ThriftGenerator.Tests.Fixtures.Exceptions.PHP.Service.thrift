include "Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.thrift"
include "Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.Exception.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP

service Service {
    list<Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.DTO1> throwsException(1:i32 int, 2:Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.DTO1 test) throws (1:Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.Exception.TestException TestException)
}