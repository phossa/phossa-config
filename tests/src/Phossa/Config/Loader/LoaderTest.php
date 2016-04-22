<?php
namespace Phossa\Config\Loader;

/**
 * Loader test case.
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * load ini
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['test' => 'wow'], Loader::load(__DIR__ . '/config_good.ini')
        );
    }

    /**
     * load php
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad2()
    {
        $this->assertEquals(
            ['test' => 'wow'], Loader::load(__DIR__ . '/config_good.php')
        );
    }

    /**
     * load json
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['test' => 'wow'], Loader::load(__DIR__ . '/config_good.json')
        );
    }

    /**
     * load xml
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad4()
    {
        $this->assertEquals(
            ['test' => 'wow'], Loader::load(__DIR__ . '/config_good.xml')
        );
    }

    /**
     * unknown type
     *
     * @covers Phossa\Config\Loader\Loader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "file suffix"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_SUFFIX_UNKNOWN
     */
    public function testLoad5()
    {
        $this->assertEquals(
            ['test' => 'wow'], Loader::load(__DIR__ . '/config_good.unknown')
        );
    }
}

