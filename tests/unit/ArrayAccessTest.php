<?php
use \PetrGrishin\ArrayAccess\ArrayAccess;
use PetrGrishin\ArrayMap\ArrayMap;

/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class ArrayAccessTest extends PHPUnit_Framework_TestCase {
    private $testArray = array(
        0 => 'value0',
        'key1' => 'value1',
        'key2' => 2,
        'key3' => array(
            'key31' => array(1, 2, 3),
            'key32' => 'value32',
            'keyNullValue' => null,
        ),
        'keyNullValue' => null,
    );

    public function testCreateInstance() {
        $instance = new ArrayAccess();
        $this->assertInstanceOf(ArrayAccess::className(), $instance);
    }

    public function testCreateInstanceByStaticMethod() {
        $instance = ArrayAccess::create();
        $this->assertInstanceOf(ArrayAccess::className(), $instance);
    }

    public function testCopy() {
        $instance = ArrayAccess::create($this->testArray);
        $instanceCopy = $instance->copy();
        $this->assertInstanceOf(ArrayAccess::className(), $instance);
        $this->assertInstanceOf(ArrayAccess::className(), $instanceCopy);
        $this->assertEquals($this->testArray, $instance->getArray());
        $this->assertEquals($this->testArray, $instanceCopy->getArray());
        $this->assertNotEquals(spl_object_hash($instance), spl_object_hash($instanceCopy));
    }

    public function testCreateEmptyArray() {
        $instance = ArrayAccess::create();
        $this->assertEquals(array(), $instance->getArray());
    }

    public function testReturnArray() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray, $instance->getArray());
    }

    public function testGetValueByPath() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key3']['key31'], $instance->getValue('key3.key31'));
    }

    public function testReturnValueBySimpleKey() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key1'], $instance->getValue('key1'));
    }

    public function testReturnNullValueBySimpleKey() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertNull($instance->getValue('keyNullValue'));
        $this->assertNull($instance->getValue('key3.keyNullValue'));
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testNotFoundValue() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('notExistKey');
    }

    /**
     * @depends testNotFoundValue
     */
    public function testGetDefaultValue() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertFalse($instance->getValue('notExistKey', false));
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testInvalidPathNotExistKey() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('key3.notExistKey');
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testInvalidPathNotArray() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->getValue('key3.key32.notExistKey');
    }

    public function testSetValueBySimplePath() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key1'], $instance->getValue('key1'));
        $instance->setValue('key1', true);
        $this->assertNotEquals($this->testArray['key1'], $instance->getValue('key1'));
        $this->assertTrue($instance->getValue('key1'));
    }

    public function testSetValueByPath() {
        $instance = ArrayAccess::create($this->testArray);
        $this->assertEquals($this->testArray['key3']['key32'], $instance->getValue('key3.key32'));
        $instance->setValue('key3.key32', true);
        $this->assertNotEquals($this->testArray['key3']['key32'], $instance->getValue('key3.key32'));
        $this->assertTrue($instance->getValue('key3.key32'));
    }

    public function testSetValueByNotExistPath() {
        $instance = ArrayAccess::create();
        $this->assertEquals(array(), $instance->getArray());
        $instance->setValue('notExistKye1.notExistKye2.notExistKye3', true);
        $this->assertEquals(array(
                'notExistKye1' => array(
                    'notExistKye2' => array(
                        'notExistKye3' => true
                    )
                )
            ),
            $instance->getArray()
        );
        $this->assertTrue($instance->getValue('notExistKye1.notExistKye2.notExistKye3'));
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testSetValueByPathWithScalarValue() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->setValue('key3.key32.notExistKye', true);
    }

    public function testRemoveElementByPath() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->remove('key3.key32');
        $this->assertNotEquals($this->testArray, $instance->getArray());
        unset($this->testArray['key3']['key32']);
        $this->assertEquals($this->testArray, $instance->getArray());
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testRemoveElementByInvalidPath() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->remove('key3.key32.notExistKye');
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testRemoveElementByInvalidKeyInPath() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->remove('key3.notExistKye');
    }

    /**
     * @expectedException \PetrGrishin\ArrayAccess\Exception\ArrayAccessException
     */
    public function testRemoveElementByNotExistKeyInPath() {
        $instance = ArrayAccess::create($this->testArray);
        $instance->remove('notExistKye1.notExistKye2');
    }

    public function testMap() {
        $instance = new ArrayAccess();
        $this->assertInstanceOf(ArrayMap::className(), $instance->getMap());
    }

    public function testSimpleMapping() {
        $original = array(1, 2, 3);
        $instance = ArrayAccess::create($original);
        $instance->getMap()->map(function ($value) {
            return $value * 2;
        });
        $this->assertEquals(array(2, 4, 6), $instance->getArray());
        $instance->getMap()->map(function ($value) {
            return $value * 2;
        });
        $this->assertEquals(array(4, 8, 12), $instance->getArray());
    }

    public function testKeyMapping() {
        $original = array(1 => 1, 2 => 2, 3 => 3);
        $instance = ArrayAccess::create($original);
        $instance->getMap()->map(function ($value, $key) {
            return array(($key - 1) => $value * 2);
        });
        $this->assertEquals(array(0 => 2, 1 => 4, 2 => 6), $instance->getArray());
    }
}
