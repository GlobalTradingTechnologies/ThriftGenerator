namespace php Gtt.ThriftGenerator.Example.Calculator.Source.Service

struct StructWork {
    1: i32 num1 = 0,
    2: i32 num2,
    3: i32 op,
    4: string comment
}

exception ExceptionInvalidOperation {
    1: i32 what,
    2: string why
}

service Calculator {
    void ping(),
    i32 add(1:i32 num1, 2:i32 num2),
    double calculate(1:i32 logid, 2:StructWork w) throws (1:ExceptionInvalidOperation Calculator_calculate_ExceptionInvalidOperation)
}