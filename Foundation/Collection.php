<?php

namespace m\Foundation;


/**
 * A generic data collection class.  This can be viewed as a
 * parameter bag or advanced StdObject.
 * 
 * @package m\Foundation
 */
class Collection
{

    /**
     * @var array The collection data
     */
    protected $_data;

    /**
     * Constructs the collection object with the given data.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_data = $data;
    }

    /**
     * Sets an item in the collection data.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Foundation\Collection
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Merges the given array of data with the collection data.
     *
     * @param array $data
     * @return \m\Foundation\Collection
     */
    public function setMany(array $data)
    {
        foreach($data as $key => $value) {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Replaces all of the collection data with the given array.
     *
     * @param array $data
     * @return \m\Foundation\Collection
     */
    public function setAll(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Returns a single item from the collection data.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Returns an array containing any of the requested items found.
     *
     * @param string|array $keys
     * @return array
     */
    public function getMany($keys)
    {
        if (!is_array($keys))
            $keys = func_get_args();

        $newArray = array();

        foreach($keys as $key) {
            if (isset($this->_data[$key])) {
                $newArray[$key] = $this->_data[$key];
            }
        }

        return $newArray;
    }

    /**
     * Returns all of the collection data.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_data;
    }

    /**
     * Clear a single item from the collection data.
     *
     * @param string $key
     * @return \m\Foundation\Collection
     */
    public function clear($key)
    {
        unset($this->_data[$key]);

        return $this;
    }

    /**
     * Clear any matching items from the collection data.
     *
     * @param string|array $keys
     * @return \m\Foundation\Collection
     */
    public function clearMany($keys)
    {
        if (!is_array($keys))
            $keys = func_get_args();

        foreach($keys as $key) {
            unset($this->_data[$key]);
        }

        return $this;
    }

    /**
     * Clear all of the collection data.
     *
     * @return \m\Foundation\Collection
     */
    public function clearAll()
    {
        $this->_data = array();

        return $this;
    }

    /**
     * Returns true if the given key(s) are stored in the collection data.
     *
     * @param string|array $key
     * @return bool
     */
    public function has($key)
    {
        $keys = (func_num_args() > 1) ? func_get_args() : (array) $key;

        foreach ($keys as $key) {
            if(!array_key_exists($key, $this->_data))
                return false;
        }

        return true;
    }

    /**
     * Gets an array of item keys stored in the collection.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_data);
    }

    /**
     * Public getter access.
     *
     * @param string $key
     * @return null
     */
    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Public setter access.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

}