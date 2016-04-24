<?php
namespace Phossa\Config\Loader;

/**
 * JsonLoader test case.
 */
class JsonLoaderTest extends \PHPUnit_Framework_TestCase
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
     * normal
     *
     * @covers Phossa\Config\Loader\JsonLoader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['test' => 'json'], JsonLoader::load(__DIR__ . '/conf/config_good.json')
        );
    }

    /**
     * No file found
     *
     * @covers Phossa\Config\Loader\JsonLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Load config file .*error"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_LOAD_ERROR
     */
    public function testLoad2()
    {
        $this->assertEquals(
            ['test' => 'json'], JsonLoader::load(__DIR__ . '/conf/nosuchfile.json')
        );
    }

    /**
     * bad format
     *
     * @covers Phossa\Config\Loader\JsonLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Syntax error"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_FORMAT_ERROR
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['test' => 'json'], JsonLoader::load(__DIR__ . '/conf/config_bad.json')
        );
    }
}

