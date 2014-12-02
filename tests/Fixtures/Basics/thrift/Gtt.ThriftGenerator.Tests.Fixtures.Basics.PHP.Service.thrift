namespace php Gtt.ThriftGenerator.Tests.Fixtures.Basics.PHP

service Service {
    string returnsString(1:i32 int, 2:string string, 3:double double, 4:double float),
    void returnsNothing(1:i32 int, 2:string string),
    void returnsNothingWithVoidAnnotation(1:i32 int, 2:string string),
    string staticMethod()
}