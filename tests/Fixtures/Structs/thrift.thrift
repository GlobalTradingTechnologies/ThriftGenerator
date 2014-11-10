namespace php Gtt\ThriftGenerator\Tests\Fixtures\Structs\Test

struct Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 {
    1: string one = "string",
    2: i32 two = 123,
    3: \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 three
}
struct Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO1 {
    1: i32 one = 1,
    2: \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO1 two,
    3: \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 three
}



service Gtt\ThriftGenerator\Tests\Fixtures\Structs\Test {
    \Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO1 receivesDTOAndReturnsDTO(1:i32 int, 2:string string, 3:\Gtt\ThriftGenerator\Tests\Fixtures\Structs\DTO\DTO2 dto2)
}