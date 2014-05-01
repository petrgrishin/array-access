<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace Util;


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
        $this->data = $data ?: array();
        $this->pathDelimiter = $pathDelimiter ?: '.';
    }

    public function toArray() {
        return $this->data;
    }

    /**
     * @param string $path
     * @param null|string $defaultValue
     * @throws \Exception
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
        throw new \Exception(sprintf('Not found value by key `%s`', $path));
    }
}
