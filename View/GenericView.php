<?php

namespace m\View;
use m\Http\Response;

class GenericView extends Response
{

    /**
     * @var array Data for the view
     */
    protected $_data = array();

    /**
     * @var string View file directory
     */
    protected $_dir;

    /**
     * Constructor.
     *
     * @param string $directory
     * @throws \InvalidArgumentException
     */
    public function __construct($directory)
    {
        $this->setDir($directory);
    }

    /**
     * Sets the directory for the view files.
     *
     * @param string $directory
     * @throws \InvalidArgumentException
     * @return \m\View
     */
    public function setDir($directory)
    {
        if (!is_dir($directory))
            throw new \InvalidArgumentException('m\View\GenericView: Given string "'.$directory.'" is not a valid directory.');

        $this->_dir = (string) rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * Returns the set view directory.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * Pass a single variable to the view data.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\View
     */
    public function pass($key, $value)
    {
        $this->_data[$key] = $value;

        return $this;
    }

    /**
     * Pass several variables to the view data.
     *
     * @param array $data
     * @return \m\View
     */
    public function passMany(array $data)
    {
        $this->_data = array_merge($this->_data, $data);

        return $this;
    }

    /**
     * Reset all of the variables in the view data to the ones given.
     *
     * @param array $data
     * @return \m\View
     */
    public function passAll(array $data)
    {
        $this->_data = $data;

        return $this;
    }

    /**
     * Returns a single item from the view data.
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
     * Returns all of the view data.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_data;
    }

    /**
     * Clear a single item from the view data.
     *
     * @param string $key
     * @return \m\View
     */
    public function clear($key)
    {
        unset($this->_data[$key]);

        return $this;
    }

    /**
     * Clear any matching items from the view data.
     *
     * @param string|array $keys
     * @return \m\View\GenericView
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
     * Clear all of the view data.
     *
     * @return \m\View\GenericView
     */
    public function clearAll()
    {
        $this->_data = array();

        return $this;
    }

    /**
     * Returns true if the given key(s) are stored in the view data.
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
     * Public getter access.
     *
     * @param string $key
     * @return null
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Public setter access.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Fetches a view file and captures it.
     *
     * @param string $file
     * @return string
     */
    public function fetch($file)
    {
        extract($this->_data);
        ob_start();
        include $this->_dir.$file;
        return ob_get_clean();
    }

    /**
     * Fetched the rendered view file and writes it to the response.
     *
     * @param string $file
     * @return string
     */
    public function fetchWrite($file)
    {
        return $this->_body = $this->fetch($file);
    }

}