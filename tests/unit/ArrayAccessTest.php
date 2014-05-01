<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class MainTest extends PHPUnit_Framework_TestCase {
    private $testArray = array(
        0 => 'value0',
        'key1' => 'value1',
        'key2' => 2
    );

    public function testCreateInstance() {
        $instance = new \Util\ArrayAccess();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }

    public function testCreateInstanceByStaticMethod() {
        $instance = \Util\ArrayAccess::create();
        $this->assertInstanceOf(\Util\ArrayAccess::className(), $instance);
    }

    public function testCreateEmptyArray() {
        $instance = \Util\ArrayAccess::create();
        $this->assertEquals(array(), $instance->toArray());
    }

    public function testReturnArray() {
        $instance = \Util\ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray, $instance->toArray());
    }
}
