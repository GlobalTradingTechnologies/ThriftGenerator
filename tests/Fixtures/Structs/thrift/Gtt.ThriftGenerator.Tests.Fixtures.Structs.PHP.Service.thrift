include "Gtt.ThriftGenerator.Tests.Fixtures.Structs.PHP.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.Structs.PHP

service Service {
    Gtt.ThriftGenerator.Tests.Fixtures.Structs.PHP.DTO.DTO1 receivesDTOAndReturnsDTO(1:i32 int, 2:string string, 3:Gtt.ThriftGenerator.Tests.Fixtures.Structs.PHP.DTO.DTO2 dto2)
}