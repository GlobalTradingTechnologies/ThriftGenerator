namespace php Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\Test

struct Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO1 {
    1: list<\Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO1> one,
    2: list<\Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO2> two
}
struct Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO2 {
    1: list<\Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO2> one
}



service Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\Test {
    list<\Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO1> receivesListOfDTOsAndReturnsListOfDTOs(1:list<\Gtt\ThriftGenerator\Tests\Fixtures\ContainerTypes\DTO\DTO1> listOfDTOs),
    list<string> receiveArrayReturnsArrayWithNativeAnnotations(1:list<string> array),
    list<i32> receiveArrayReturnsArrayWithExplicitAnnotations(1:list<string> strings)
}