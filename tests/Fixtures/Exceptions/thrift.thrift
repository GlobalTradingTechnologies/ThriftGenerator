namespace php Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Test

struct Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO1 {
    1: \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO1 one,
    2: list<\Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO2> two,
    3: string three = "string"
}
struct Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO2 {
    1: list<\Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO2> one
}

exception Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Exception\TestException {
    1: i32 one = 1,
    2: \Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO1 two,
    3: list<\Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO2> three
}

service Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Test {
    list<\Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO1> throwsException(1:i32 int, 2:\Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\DTO\DTO1 test) throws (1:Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Exception\TestException Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Test_throwsException_Gtt\ThriftGenerator\Tests\Fixtures\Exceptions\Exception\TestException)
}