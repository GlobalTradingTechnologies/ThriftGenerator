namespace php Gtt.ThriftGenerator.Tests.Fixtures.Exceptions

struct DTODTO1 {
    1: DTODTO1 one,
    2: list<DTODTO2> two,
    3: string three = "string"
}
struct DTODTO2 {
    1: list<DTODTO2> one
}

exception ExceptionTestException {
    1: i32 one = 1,
    2: DTODTO1 two,
    3: list<DTODTO2> three
}

service Test {
    list<DTODTO1> throwsException(1:i32 int, 2:DTODTO1 test) throws (1:ExceptionTestException Test_throwsException_ExceptionTestException)
}