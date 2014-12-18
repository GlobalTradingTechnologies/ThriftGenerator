include "Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes.PHP.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes.PHP

service Service {
    list<Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes.PHP.DTO.DTO1> receivesListOfDTOsAndReturnsListOfDTOs(1:list<Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes.PHP.DTO.DTO1> listOfDTOs),
    map<string, string> receiveArrayReturnsArrayWithNativeAnnotations(1:map<string, string> array),
    list<i32> receiveArrayReturnsArrayWithExplicitAnnotations(1:list<string> strings)
}