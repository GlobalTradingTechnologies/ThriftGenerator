include "Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.AnotherDTO.DTO.thrift"

namespace php Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.DTO

struct DTO1 {
    1: i32 one = 1,
    2: DTO2 two,
    3: DTO3 three
}
struct DTO2 {
    1: string one = "string",
    2: i32 two = 123,
    3: DTO3 three
}
struct DTO3 {
    1: DTO4 one
}
struct DTO4 {
    1: Gtt.ThriftGenerator.Tests.Fixtures.SeveralClasses.PHP.AnotherDTO.DTO.DTO2 one
}