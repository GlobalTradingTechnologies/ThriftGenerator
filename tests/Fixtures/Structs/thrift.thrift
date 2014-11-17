namespace php Gtt.ThriftGenerator.Tests.Fixtures.Structs

struct DTODTO2 {
    1: string one = "string",
    2: i32 two = 123,
    3: DTODTO2 three
}
struct DTODTO1 {
    1: i32 one = 1,
    2: DTODTO1 two,
    3: DTODTO2 three
}



service Test {
    DTODTO1 receivesDTOAndReturnsDTO(1:i32 int, 2:string string, 3:DTODTO2 dto2)
}