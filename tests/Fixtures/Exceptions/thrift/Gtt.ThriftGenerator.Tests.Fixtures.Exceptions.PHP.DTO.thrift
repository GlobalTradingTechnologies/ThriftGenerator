namespace php Gtt.ThriftGenerator.Tests.Fixtures.Exceptions.PHP.DTO

struct DTO1 {
    1: DTO1 one,
    2: list<DTO2> two,
    3: string three = "string"
}
struct DTO2 {
    1: list<DTO2> one
}