<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\ArrayAccess;


use PetrGrishin\ArrayAccess\Exception\ArrayAccessException;

class ArrayAccess {
    /** @var array */
    private $data;
    /** @var string */
    private $pathDelimiter;

    public static function className() {
        return get_called_class();
    }

    public static function create(array $data = null, $pathDelimiter = null) {
        return new static($data, $pathDelimiter);
    }

    public function __construct(array $data = null, $pathDelimiter = null) {
        $this->setArray($data ?: array());
        $this->setPathDelimiter($pathDelimiter ?: '.');
    }

    public function setPathDelimiter($delimiter) {
        $this->pathDelimiter = $delimiter;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setArray(array $data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * @param string $path
     * @param null|string $defaultValue
     * @throws Exception\ArrayAccessException
     * @return mixed
     */
    public function getValue($path, $defaultValue = null) {
        $array = $this->data;
        if (array_key_exists($path, $array)) {
            return $array[$path];
        }
        $keys = explode($this->pathDelimiter, $path);
        do {
            $key = array_shift($keys);
            if (!array_key_exists($key, $array)) {
                break;
            }
            $value = $array[$key];
            if (!$keys) {
                return $value;
            }
            if (!is_array($value)) {
                break;
            }
            $array = $value;
        } while ($keys);
        if ($defaultValue !== null) {
            return $defaultValue;
        }
        throw new ArrayAccessException(sprintf('Not found value by key `%s`', $path));
    }

    /**
     * @param $path
     * @param $value
     * @return $this
     * @throws Exception\ArrayAccessException
     */
    public function setValue($path, $value) {
        $array = & $this->data;
        $keys = explode($this->pathDelimiter, $path);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!array_key_exists($key, $array)) {
                $array[$key] = array();
            } elseif (!is_array($array[$key])) {
                throw new ArrayAccessException(sprintf('Value is not array'));
            }
            $array = & $array[$key];
        }
        $key = array_shift($keys);
        $array[$key] = $value;
        return $this;
    }
}
