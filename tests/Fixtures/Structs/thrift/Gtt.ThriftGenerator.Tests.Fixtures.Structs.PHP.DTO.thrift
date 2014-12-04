namespace php Gtt.ThriftGenerator.Tests.Fixtures.Structs.PHP.DTO

struct DTO2 {
    1: string one = "string",
    2: i32 two = 123,
    3: DTO2 three
}
struct DTO1 {
    1: i32 one = 1,
    2: DTO1 two,
    3: DTO2 three
}