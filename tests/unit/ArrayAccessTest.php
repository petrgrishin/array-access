<?php
use \PetrGrishin\ArrayAccess\ArrayAccess;

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

    public function testSimpleMapping() {
        $original = array(1, 2, 3);
        $instance = ArrayAccess::create($original);
        $instance->map(function ($value) {
            return $value * 2;
        });
        $this->assertEquals(array(2, 4, 6), $instance->getArray());
    }

    public function testKeyMapping() {
        $original = array(1 => 1, 2 => 2, 3 => 3);
        $instance = ArrayAccess::create($original);
        $instance->map(function ($value, $key) {
            return array(($key - 1) => $value * 2);
        });
        $this->assertEquals(array(0 => 2, 1 => 4, 2 => 6), $instance->getArray());
    }

    public function testMergeWith() {
        $original = array(1, 2, 3);
        $instance = ArrayAccess::create($original);
        $instance->mergeWith(array(4, 5, 6), false);
        $this->assertEquals(array(1, 2, 3, 4, 5, 6), $instance->getArray());
    }

    public function testRecursiveMergeWith() {
        $original = array('a' => array(1), 'b', 'c');
        $instance = ArrayAccess::create($original);
        $instance->mergeWith(array('a' => array(2), 'd', 'e'));
        $this->assertEquals(array('a' => array(1, 2), 'b', 'c', 'd', 'e'), $instance->getArray());
    }

    public function testFiltering() {
        $original = array('a' => 1, 'b' => 2, 'c' => 3);
        $instance = ArrayAccess::create($original);
        $instance->filter(function ($value) {
            return $value > 2;
        });
        $this->assertEquals(array('c' => 3), $instance->getArray());
    }

    public function testFilteringUseKeys() {
        $original = array('a' => 1, 'b' => 2, 'c' => 3);
        $instance = ArrayAccess::create($original);
        $instance->filter(function ($value, $key) {
            return $key === 'c';
        });
        $this->assertEquals(array('c' => 3), $instance->getArray());
    }
}
