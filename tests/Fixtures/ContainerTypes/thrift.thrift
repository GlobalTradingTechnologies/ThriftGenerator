namespace php Gtt.ThriftGenerator.Tests.Fixtures.ContainerTypes

struct DTODTO1 {
    1: list<DTODTO1> one,
    2: list<DTODTO2> two
}
struct DTODTO2 {
    1: list<DTODTO2> one
}



service Test {
    list<DTODTO1> receivesListOfDTOsAndReturnsListOfDTOs(1:list<DTODTO1> listOfDTOs),
    list<string> receiveArrayReturnsArrayWithNativeAnnotations(1:list<string> array),
    list<i32> receiveArrayReturnsArrayWithExplicitAnnotations(1:list<string> strings)
}