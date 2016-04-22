<?php
namespace Phossa\Config\Helper;

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
    protected $class_name;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->class_name = '\\Phossa\\Config\\Helper\\Environment';
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
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
    protected function getMethod($className, $methodName) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Tests Environment::matchEnv()
     *
     * @covers Phossa\Config\Helper\Environment::matchEnv()
     */
    public function testMatchEnv()
    {
        $method = $this->getMethod($this->class_name, 'matchEnv');

        // existing env
        putenv('test=bingo');
        $this->assertEquals('bingo', $method->invokeArgs(null, ['test']));

        // remove env
        putenv('test');
        $this->assertFalse($method->invokeArgs(null, ['test']));

        // super globals
        $_SERVER['test'] = 'bingo';
        $this->assertEquals('bingo', $method->invokeArgs(null, ['_SERVER.test']));

        unset($_SERVER['test']);
        $this->assertFalse($method->invokeArgs(null, ['_SERVER.test']));
    }

    /**
     * Tests Environment::deReference()
     *
     * @covers Phossa\Config\Helper\Environment::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Unknown environment"
     */
    public function testDeReference1()
    {
        $method = $this->getMethod($this->class_name, 'deReference');
        $this->assertEquals('bingowow', $method->invokeArgs(null, ['${test}wow']));
    }

    /**
     * Tests Environment::deReference()
     *
     * @covers Phossa\Config\Helper\Environment::deReference()
     */
    public function testDeReference2()
    {
        $method = $this->getMethod($this->class_name, 'deReference');

        putenv('test=bingo');
        $this->assertEquals('bingowow', $method->invokeArgs(null, ['${test}wow']));

        putenv('bingo=wow');
        $this->assertEquals('wowwow', $method->invokeArgs(null, ['${${test}}wow']));

        putenv('test');
        putenv('bingo');
    }

    /**
     * @covers Phossa\Config\Helper\Environment::parse()
     */
    public function testParse()
    {
        $method = $this->getMethod($this->class_name, 'parse');

        // existing env
        $str = '
            # comment1
            test1=bingo1
            test2=${test1}bingo2 # comment2

            # test3=wow
        ';
        $this->assertEquals(
            ['test1' => 'bingo1', 'test2' => '${test1}bingo2'],
            $method->invokeArgs(null, [$str])
        );
    }

    /**
     * Tests Environment::load()
     *
     * @covers Phossa\Config\Helper\Environment::load()
     */
    public function testLoad1()
    {
        // preset $_SERVER
        $_SERVER['test'] = 'xxx';

        Environment::load(__DIR__ . '/envfile.txt');

        $this->assertEquals('bing', getenv('test1'));
        $this->assertEquals('bingwow', getenv('test2'));
        $this->assertEquals('space1 space2', getenv('test3'));
        $this->assertEquals('wowwow', getenv('test4'));
        $this->assertEquals('xxxwow', getenv('test5'));

        // delete used envs
        putenv('test1');
        putenv('test2');
        putenv('test3');
        putenv('test4');
        putenv('test4');
        putenv('bingo');
        unset($_SERVER['test']);
    }

    /**
     * Test load env file failure
     *
     * @covers Phossa\Config\Helper\Environment::load()
     * @expectedException Phossa\Config\Exception\NotFoundException
     * @expectedExceptionMessageRegExp "not found or not readable"
     */
    public function testLoad2()
    {
        Environment::load(__DIR__ . '/nonexitfile.txt');
    }
}

