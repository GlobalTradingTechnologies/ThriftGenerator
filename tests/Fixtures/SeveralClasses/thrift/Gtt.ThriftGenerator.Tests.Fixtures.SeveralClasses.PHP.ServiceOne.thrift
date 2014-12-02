include "Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP

service ServiceOne {
    void worksWithDTOsFromSingleNamespaces(1:Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.DTO.DTO1 dto1)
}