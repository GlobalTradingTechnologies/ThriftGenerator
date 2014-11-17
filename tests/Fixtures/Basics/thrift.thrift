namespace php Gtt.ThriftGenerator.Tests.Fixtures.Basics





service Test {
    string returnsString(1:i32 int, 2:string string),
    void returnsNothing(1:i32 int, 2:string string),
    void returnsNothingWithVoidAnnotation(1:i32 int, 2:string string),
    string staticMethod()
}