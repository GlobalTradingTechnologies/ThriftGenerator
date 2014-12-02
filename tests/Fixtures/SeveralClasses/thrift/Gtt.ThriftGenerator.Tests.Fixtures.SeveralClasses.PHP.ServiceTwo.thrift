include "Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.DTO.thrift"
include "Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.AnotherDTO.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP

service ServiceTwo {
    void worksWithDTOsFromSeveralNamespaces(1:Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.DTO.DTO2 dto1, 2:Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.AnotherDTO.DTO.DTO2 dto2)
}