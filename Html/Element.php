<?php

namespace m\Html;


/**
 * An object representation of an HTML element.
 * 
 * @package m\Html
 */
abstract class Element
{

    /**
     * @var array Element attributes
     */
    protected $_attr = array();

    /**
     * Renders the attribute string for the element.
     *
     * @return string
     */
    protected function renderAttributes()
    {
        $output = '';

        foreach($this->_attr as $key => $value) {
            $output .= $key.' = "'.$value.'" ';
        }

        return $output;
    }

    /**
     * Sets an attribute for the element.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Html\Element
     */
    public function setAttribute($key, $value)
    {
        $this->_attr[$key] = $value;

        return $this;
    }

    /**
     * Sets multiple element attributes.
     *
     * @param array $data
     * @return \m\Html\Element
     */
    public function setAttributes(array $data)
    {
        $this->_attr = array_merge($this->_attr, $data);

        return $this;
    }

    /**
     * Returns the requested attribute if it exists.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        return isset($this->_attr[$key]) ? $this->_attr[$key] : null;
    }

    /**
     * Returns the entire attribute array.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attr;
    }

    /**
     * Returns true if the requested attribute exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->_attr[$key]);
    }

    /**
     * Returns true if all of the requested attributes exist.
     *
     * @param string|array $key
     * @return bool
     */
    public function hasAttributes($key)
    {
        $keys = (func_num_args() > 1) ? func_get_args() : (array) $key;

        foreach ($keys as $key) {
            if(!array_key_exists($key, $this->_data))
                return false;
        }

        return true;
    }

    /**
     * Returns the keys of all of the set attributes.
     *
     * @return array
     */
    public function attributesKeys()
    {
        return array_keys($this->_attr);
    }

    /**
     * Clear the requested attribute, unless it is "name".
     *
     * @param string $key
     * @return \m\Html\Element
     */
    public function clearAttribute($key)
    {
        if ($key != 'name')
            unset($this->_attr[$key]);

        return $this;
    }

    /**
     * Clears all of the set attributes except "name".
     *
     * @return \m\Html\Element
     */
    public function clearAttributes()
    {
        $name = $this->_attr['name'];

        $this->_attr = array(
            'name'  => $name
        );

        return $this;
    }

    /**
     * Sets the value for the element.
     *
     * @param string $value
     * @return \m\Html\Element
     */
    public function setValue($value)
    {
        $this->_attr['value'] = (string) $value;

        return $this;
    }

    /**
     * Gets the current value of the element.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_attr['value'];
    }

    /**
     * Compiles the element to a string.
     *
     * @return string
     */
    abstract public function render();

}