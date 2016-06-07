<?php
namespace Phossa\Config\Env;

/**
 * Environment test case.
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * class name
     *
     * @var    string
     * @access protected
     */
    protected $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Environment();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * get protected method to test
     *
     * @param  string $className
     * @param  string $methodName
     * @return \ReflectionMethod
     * @access protected
     */
    protected function getMethod($methodName) {
        $class = new \ReflectionClass($this->object);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @covers Phossa\Config\Env\Environment::matchEnv()
     */
    public function testMatchEnv()
    {
        $method = $this->getMethod('matchEnv');

        // existing env
        putenv('test=bingo');
        $this->assertEquals('bingo',
            $method->invokeArgs($this->object, ['test']));

        // remove env
        putenv('test');
        $this->assertFalse($method->invokeArgs($this->object, ['test']));

        // super globals
        $_SERVER['test'] = 'bingo';
        $this->assertEquals('bingo',
            $method->invokeArgs($this->object, ['_SERVER.test']));

        unset($_SERVER['test']);
        $this->assertFalse($method->invokeArgs($this->object, ['_SERVER.test']));
    }

    /**
     * @covers Phossa\Config\Env\Environment::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Unknown environment"
     */
    public function testDeReference1()
    {
        $method = $this->getMethod('deReference');
        $this->assertEquals('bingowow',
            $method->invokeArgs($this->object, ['${test}wow']));
    }

    /**
     * @covers Phossa\Config\Env\Environment::deReference()
     */
    public function testDeReference2()
    {
        $method = $this->getMethod('deReference');

        putenv('test=bingo');
        $this->assertEquals('bingowow',
            $method->invokeArgs($this->object, ['${test}wow']));

        putenv('bingo=wow');
        $this->assertEquals('wowwow',
            $method->invokeArgs($this->object, ['${${test}}wow']));

        putenv('test');
        putenv('bingo');
    }

    /**
     * @covers Phossa\Config\Env\Environment::parse()
     */
    public function testParse()
    {
        $method = $this->getMethod('parse');

        // existing env
        $str = '
            # comment1
            test1=bingo1
            test2=${test1}bingo2 # comment2

            # test3=wow
        ';
        $this->assertEquals(
            ['test1' => 'bingo1', 'test2' => '${test1}bingo2'],
            $method->invokeArgs($this->object, [$str])
        );
    }

    /**
     * @covers Phossa\Config\Env\Environment::load()
     */
    public function testLoad1()
    {
        // preset $_SERVER
        $_SERVER['test'] = 'xxx';

        $this->object->load(__DIR__ . '/envfile.txt');

        $this->assertEquals('bing', getenv('test1'));
        $this->assertEquals('bingwow', getenv('test2'));
        $this->assertEquals('space1 space2', getenv('test3'));
        $this->assertEquals('wowwow', getenv('test4'));
        $this->assertEquals('xxxwow', getenv('test5'));
        $this->assertEquals('envfile.txt/wow', getenv('test7'));

        // delete used envs
        putenv('test1');
        putenv('test2');
        putenv('test3');
        putenv('test4');
        putenv('test5');
        putenv('test6');
        putenv('test7');
        putenv('bingo');
        unset($_SERVER['test']);
    }

    /**
     * Test load env file failure
     *
     * @covers Phossa\Config\Env\Environment::load()
     * @expectedException Phossa\Config\Exception\NotFoundException
     * @expectedExceptionMessageRegExp "not found or not readable"
     */
    public function testLoad2()
    {
        $this->object->load(__DIR__ . '/nonexitfile.txt');
    }
}

