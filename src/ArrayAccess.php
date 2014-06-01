<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\ArrayAccess;


use PetrGrishin\ArrayAccess\Exception\ArrayAccessException;
use PetrGrishin\ArrayMap\ArrayMap;

class ArrayAccess {
    /** @var array */
    private $data;
    /** @var string */
    private $pathDelimiter;

    /**
     * @return string
     */
    public static function className() {
        return get_called_class();
    }

    /**
     * @param array|null $data
     * @param string|null $pathDelimiter
     * @return static
     */
    public static function create(array $data = null, $pathDelimiter = null) {
        return new static($data, $pathDelimiter);
    }

    /**
     * @param array|null $data
     * @param string|null $pathDelimiter
     */
    public function __construct(array $data = null, $pathDelimiter = null) {
        $this->setArray($data ?: array());
        $this->setPathDelimiter($pathDelimiter ?: '.');
    }

    /**
     * @param string $delimiter
     * @return $this
     */
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
    public function getArray() {
        return $this->data;
    }

    /**
     * @param string $path
     * @param mixed $defaultValue
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
     * @param string $path
     * @param mixed $value
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

    /**
     * @param string $path
     * @return $this
     * @throws Exception\ArrayAccessException
     */
    public function remove($path) {
        $array = & $this->data;
        $keys = explode($this->pathDelimiter, $path);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!array_key_exists($key, $array)) {
                throw new ArrayAccessException(sprintf('Not exist key'));
            } elseif (!is_array($array[$key])) {
                throw new ArrayAccessException(sprintf('Value is not array'));
            }
            $array = & $array[$key];
        }
        $key = array_shift($keys);
        if (!array_key_exists($key, $array)) {
            throw new ArrayAccessException(sprintf('Not exist key'));
        }
        unset($array[$key]);
        return $this;
    }

    public function map($callback) {
        try {
            $this->data = ArrayMap::create($this->data)
                ->map($callback)
                ->getArray();
        } catch (\PetrGrishin\ArrayMap\Exception\ArrayMapException $e) {
            throw new ArrayAccessException(sprintf('Error when mapping: %s', $e->getMessage()), null, $e);
        }
        return $this;
    }

    public function mergeWith(array $data, $recursive = true) {
        try {
            $this->data = ArrayMap::create($this->data)
                ->mergeWith($data, $recursive)
                ->getArray();
        } catch (\PetrGrishin\ArrayMap\Exception\ArrayMapException $e) {
            throw new ArrayAccessException(sprintf('Error when merge: %s', $e->getMessage()), null, $e);
        }
        return $this;
    }
}
