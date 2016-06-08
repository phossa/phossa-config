<?php

namespace Phossa\Config;

/**
 * Parameter test case.
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Parameter
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // test data to use
        $params = [
            'test1' => '${wow1}',
            'test2' => [
                'test3' => 'wow3'
            ],
            'wow1'  => '${test2.test3}',
            'wow3'  => 'xxx'
        ];
        $this->object = new Parameter($params);
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
        $class = new \ReflectionClass('\\Phossa\\Config\\Parameter');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @covers Phossa\Config\Reference\Parameter::setReferencePattern()
     */
    public function testSetReferencePattern()
    {
        $this->object->setReferencePattern('~(%([a-zA-Z_][a-zA-Z0-9._]*+)%)~');

        // test data to use
        $data = [
            'test1' => '%wow1%',
            'test2' => [
                'test3' => '%wow3%'
            ],
            'wow1'  => '%test2.test3%',
            'wow3'  => 'xxx'
        ];
        $this->object->set(null, $data);

        $this->assertEquals('xxx', $this->object->get('test1'));
    }

    /**
     * @covers Phossa\Config\Reference\Parameter::hasReference()
     */
    public function testHasReference()
    {
        $method = $this->getMethod('hasReference');

        // no ref
        $str1 = 'dd';
        $this->assertFalse($method->invokeArgs($this->object, [ $str1 ]));

        // has ref
        $str2 = '${test}dd';
        $this->assertEquals(
            ['${test}' => 'test'],
            $method->invokeArgs($this->object, [ $str2 ])
        );

        // multiple refs
        $str3 = '${test1}dd${test2}';
        $this->assertEquals(
            ['${test1}' => 'test1', '${test2}' => 'test2'],
            $method->invokeArgs($this->object, [ $str3 ])
        );

        // recursive refs
        $str4 = '${${test1}}dd';
        $this->assertEquals(
            ['${test1}' => 'test1'],
            $method->invokeArgs($this->object, [ $str4 ])
        );
    }

    /**
     * @covers Phossa\Config\Reference\Parameter::deReference()
     */
    public function testDeReference1()
    {
        $method = $this->getMethod('deReference');

        // no ref
        $str1 = 'dd';
        $this->assertEquals(
            $str1,
            $method->invokeArgs($this->object, [ $str1 ])
        );

        // has ref
        $str2 = '${test1}dd';
        $this->assertEquals(
            'wow3dd',
            $method->invokeArgs($this->object, [ $str2 ])
        );

        // multiple refs, leveled refs
        $str3 = '${test1}dd${test2.test3}';
        $this->assertEquals(
            'wow3ddwow3',
            $method->invokeArgs($this->object, [ $str3 ])
        );

        // recursive refs
        $str4 = '${${test1}}dd';
        $this->assertEquals(
            'xxxdd',
            $method->invokeArgs($this->object, [ $str4 ])
        );

        // reference to array
        $str5 = '${test2}';
        $this->assertEquals(
            ['test3' => 'wow3'],
            $method->invokeArgs($this->object, [ $str5 ])
        );
    }

    /**
     * reference unknown
     *
     * @covers Phossa\Config\Reference\Parameter::deReference()
     */
    public function testDeReference2()
    {
        $method = $this->getMethod('deReference');

        // unknown ref, keep it untouched
        $str1 = '${yyy}dd';
        $this->assertEquals(
            $str1,
            $method->invokeArgs($this->object, [ $str1 ])
        );
    }

    /**
     * malformed reference, mix array with string
     *
     * @covers Phossa\Config\Reference\Parameter::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "Malformed"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_MALFORM
     */
    public function testDeReference3()
    {
        $method = $this->getMethod('deReference');

        // dereferenced array into a string
        $str1 = '${test2}dd';
        $method->invokeArgs($this->object, [ $str1 ]);
    }

    /**
     * reference loop
     *
     * @covers Phossa\Config\Reference\Parameter::deReference()
     * @expectedException Phossa\Config\Exception\LogicException
     * @expectedExceptionMessageRegExp "loop"
     * @expectedExceptionCode Phossa\Config\Message\Message::CONFIG_REF_LOOP
     */
    public function testDeReference4()
    {
        // set data
        $data = [
            'testX' => '${testY}',
            'testY' => '${testX}'
        ];
        $this->object->set(null, $data);

        $method = $this->getMethod('deReference');

        // loop found
        $str = '${testX}';
        $method->invokeArgs($this->object, [ $str ]);
    }

    /**
     * @covers Phossa\Config\Reference\Parameter::deReferenceArray()
     */
    public function testDeReferenceArray1()
    {
        $data = [
            'new1' => '${test1}',
            'new2' => '${test2}'
        ];

        // get() will auto dereference pool
        $this->assertEquals('wow3', $this->object->get('test1'));

        // dereference $data
        $method = $this->getMethod('deReferenceArray');
        $method->invokeArgs($this->object, [ &$data ]);

        $this->assertEquals([
            'new1' => 'wow3',
            'new2' => [ 'test3' => 'wow3' ]
        ],  $data);
    }

    /**
     * Get raw values
     *
     * @covers Phossa\Config\Reference\Parameter::getValue()
     */
    public function testGetValue1()
    {
        $method = $this->getMethod('getValue');
        $this->assertEquals('xxx',
            $method->invokeArgs($this->object, [ 'wow3' ]));
        $this->assertEquals('${test2.test3}',
            $method->invokeArgs($this->object, [ 'wow1' ]));
        $this->assertEquals('${wow1}',
            $method->invokeArgs($this->object, [ 'test1' ]));
    }

    /**
     * test super globals
     *
     * @covers Phossa\Config\Reference\Parameter::getValue()
     */
    public function testGetValue2()
    {
        $_SERVER['TEST'] = 'bingo';
        $method = $this->getMethod('getValue');

        $this->assertEquals('bingo',
            $method->invokeArgs($this->object, [ '_SERVER.TEST' ]));

        unset($_SERVER['TEST']);
    }

    /**
     * get super globals failed
     *
     * @covers Phossa\Config\Reference\Parameter::getValue()
     */
    public function testGetValue3()
    {
        $method = $this->getMethod('getValue');

        $this->assertEquals(null,
            $method->invokeArgs($this->object, [ '_SERVER.TEST' ]));
    }
}
