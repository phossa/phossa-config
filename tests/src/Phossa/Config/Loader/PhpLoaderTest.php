<?php
namespace Phossa\Config\Loader;

/**
 * PhpLoader test case.
 */
class PhpLoaderTest extends \PHPUnit_Framework_TestCase
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
     * @covers Phossa\Config\Loader\PhpLoader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['test' => 'wow', 'bingo' => 'xxx'],
            PhpLoader::load(__DIR__ . '/conf/config_good.php')
        );
    }

    /**
     * No file found
     *
     * @covers Phossa\Config\Loader\PhpLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Load config file .*error"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_LOAD_ERROR
     */
    public function testLoad2()
    {
        $this->assertEquals(
            ['test' => 'wow','bingo' => 'xxx'],
            PhpLoader::load(__DIR__ . '/conf/nosuchfile.php')
        );
    }

    /**
     * bad format
     *
     * @covers Phossa\Config\Loader\PhpLoader::load()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "not array"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_FORMAT_ERROR
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['test' => 'wow'], PhpLoader::load(__DIR__ . '/conf/config_bad.php')
        );
    }

    /**
     * test callable
     *
     * @covers Phossa\Config\Loader\PhpLoader::load()
     */
    public function testLoad4()
    {
        $this->assertEquals(
            ['test' => 'wow'], PhpLoader::load(__DIR__ . '/conf/config_callable.php')
        );
    }
}

