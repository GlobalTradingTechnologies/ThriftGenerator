include "Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.Exception

exception TestException {
    1: i32 one = 1,
    2: Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.DTO1 two,
    3: list<Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO.DTO2> three
}