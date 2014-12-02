namespace php Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes.PHP.DTO

struct DTO1 {
    1: list<DTO1> one,
    2: list<DTO2> two
}
struct DTO2 {
    1: list<DTO2> one
}