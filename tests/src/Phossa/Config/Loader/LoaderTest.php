<?php
namespace Phossa\Config\Loader;

/**
 * Loader test case.
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    Loader
     * @access private
     */
    protected $loader;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $loader = new Loader();
        $this->loader = $loader(__DIR__.'/conf');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->loader = null;
        parent::tearDown();
    }

    /**
     * getPrivateProperty
     *
     * @param 	string $propertyName
     * @return	the property
     */
    public function getPrivateProperty($propertyName, $object) {
        $reflector = new \ReflectionClass($object);
        $property  = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * test loader init
     *
     * @covers Phossa\Config\Loader\Loader::__invoke()
     */
    public function testInvoke1()
    {
        $loader = $this->loader;
        $loader(__DIR__, 'ini');

        $this->assertEquals(__DIR__,
            $this->getPrivateProperty('root_dir', $this->loader)
        );

        $this->assertEquals('ini',
            $this->getPrivateProperty('file_type', $this->loader)
        );
    }

    /**
     * unknown file type
     *
     * @covers Phossa\Config\Loader\Loader::__invoke()
     * @expectedException Phossa\Config\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp "file suffix"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_SUFFIX_UNKNOWN
     */
    public function testInvoke2()
    {
        $loader = $this->loader;
        $loader(__DIR__, 'unknown');
    }

    /**
     * unknown root  dir
     *
     * @covers Phossa\Config\Loader\Loader::__invoke()
     * @expectedException Phossa\Config\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp "nonexist or not readable"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_DIR_INVALID
     */
    public function testInvoke3()
    {
        $loader = $this->loader;
        $loader('/unknown/dir');
    }

    /**
     * load group 'config_good'
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            [ 'config_good' => [['test' => 'wow', 'bingo' => 'xxx']]],
            $this->loader->load('config_good')
        );
    }

    /**
     * load group 'config_good', env 'production'
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad2()
    {
        $this->assertEquals(
            [ 'config_good' => [
                ['test' => 'wow',  'bingo' => 'xxx'],
                ['test' => 'prod']
            ]],
            $this->loader->load('config_good', 'production')
        );
    }

    /**
     * load group 'config_good', env 'production/host1'
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['config_good' => [
                ['test' => 'wow',  'bingo' => 'xxx'],
                ['test' => 'prod'],
                ['bingo' => 'yyy']
            ]],
            $this->loader->load('config_good', 'production\\host1')
        );
    }

    /**
     * load all
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad4()
    {
        $loader = $this->loader;
        $loader(__DIR__.'/conf/production');

        $this->assertEquals(
            [
                'all' => [
                    ['all' => 'all']
                ],
                'config_good' => [
                    ['test' => 'prod'],
                    ['bingo' => 'yyy']
                ]
            ],
            $loader->load(null, 'host1')
        );
    }

    /**
     * load other type, json
     *
     * @covers Phossa\Config\Loader\Loader::load()
     */
    public function testLoad5()
    {
        $loader = $this->loader;
        $loader(__DIR__.'/conf', 'json');

        $this->assertEquals(
            [ 'config_good' => [['test' => 'json'] ]],
            $loader->load('config_good')
        );
    }
}

