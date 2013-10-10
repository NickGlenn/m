<?php

namespace m\Html;
use m\Html\Element;
use m\Http\SessionInterface;


/**
 * A helper object for building and rendering HTML forms.
 * 
 * @package m\Html
 */
class Form
{

    /**
     * @var array The form value data
     */
    protected $_data = array();

    /**
     * @var bool Enable CSRF Token
     */
    protected $_enableToken = true;

    /**
     * @var array The form fields
     */
    protected $_fields = array();

    /**
     * @var array Stores the custom field handlers/generators
     */
    protected $_handlers = array();

    /**
     * @var \m\Http\SessionInterface Session object
     */
    protected $_session;

    /**
     * Set a session object for the CSRF token.
     *
     * @param \m\Http\SessionInterface $session
     * @return \m\Html\Form
     */
    public function setSession(SessionInterface $session)
    {
        $this->_session = $session;

        return $this;
    }

    /**
     * Return the stored session object.
     *
     * @return \m\Http\SessionInterface
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Sets a field in the form.
     *
     * @param string $name
     * @param \m\Html\Element $field
     */
    public function setField($name, Element $field)
    {
        $this->_fields[$name] = $field;
    }

    /**
     * Gets a field if it is set.
     *
     * @param string $name
     * @return object|null
     */
    public function getField($name)
    {
        return isset($this->_fields[$name]) ? $this->_fields[$name] : null;
    }

    /**
     * Returns true if the requested field exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasField($name)
    {
        return isset($this->_fields[$name]);
    }

    /**
     * Clears the requested field.
     *
     * @param string $name
     * @return \m\Html\Form
     */
    public function clearField($name)
    {
        unset($this->_fields[$name]);

        return $this;
    }

    /**
     * Shortcut for setting fields.
     *
     * @param string $key
     * @param FieldAbstract $field
     */
    public function __set($key, FieldAbstract $field)
    {
        $this->_fields[$key] = $field;
    }

    /**
     * Shortcut for getting fields.
     *
     * @param string $key
     * @return object|null
     */
    public function __get($key)
    {
        return isset($this->_fields[$name]) ? $this->_fields[$name] : null;
    }

    /**
     * Compiles the form and it fields to a string.
     *
     * @return string
     */
    public function render()
    {


        $fields = $this->_fields;

        $output = $field['_start'];
        $close = $field['_close'];

        unset($field['_start'], $field['_close']);

        foreach($fields as $field) {

            $output .= $field;

        }

        return $output.$close;
    }

    /**
     * Updates the values of the stored fields.
     *
     * @param array $data
     * @return \m\Html\Form
     */
    public function update(array $data)
    {
        $this->_data = array_merge($this->_data, $data);

        return $this;
    }

    public function setValueFor($name, $value)
    {
        $this->_data[$name] = $value;

        return $this;
    }

    public function getValueFor($name, $default = null)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    public function hasValueFor($name)
    {
        return array_key_exists($this->_data, $name);
    }

    public function clearValueFor($name)
    {
        unset($this->_data[$name]);

        return $this;
    }

    public function setFieldHandler($macro, $handler)
    {
        if (!is_callable($handler) || !$handler instanceof \Closure)
            throw new \InvalidArgumentException('m\Http\Form: setFieldHandler method requires a callable $handler.');

        $this->_handlers[$macro] = $handler;

        return $this;
    }

    public function getFieldHandler($macro)
    {
        return isset($this->_handlers[$macro]) ? $this->_handlers[$macro] : function() { return 'handler not found'; };
    }

    public function makeField($macro, array $params = array())
    {
        // Get the field handler
        $handler = $this->getFieldHandler($macro);

        // Final parameter passed is the form object
        $params[] = $this;

        // Call the custom handler and capture the output
        $output = call_user_func_array($handler, $params);

        return $output;
    }

    public function __call($macro, $params)
    {
        return $this->makeField($macro, $params);
    }

    /**
     * Renders an array of values as a string.  It will also
     * change any HTML value attribute values to the %value% tag
     * to allow the string to be dynamically changed.
     * 
     * @param  array  $attributes [description]
     * @return [type]             [description]
     */
    public function renderAttributes($name, array $attributes, $default = null)
    {
        $output = '';

        foreach($attributes as $key => $value) {

            // If it's the "value" attribute, change it to %value%
            if (strtolower($key) == 'value') {
                // Set it to our catchable string
                $value = '%value%';
            }

            // Simple key="value"
            $output .= $key.' = "'.$value.'" ';
        }

        return $output;
    }

    public function renderWithValue($string, $fieldName)
    {
        $value = $this->getValueFor($fieldName);

        return str_replace('%value%', $value, $string);
    }

    public function start($action = '', $method = 'POST', $multipart = false)
    {
        // Create the form start tag
        $output =   '<form action="'.$action.'" method="'.($method == 'GET' ? 'GET' : 'POST').'"'.($multipart ? 'enctype="multipart/form-data"' : '').' />';

        // If we have a session object and token is enabled, generate the field
        if ($this->_session && $this->_enableToken)
            $output .= '<input type="hidden" name="_token" value="'.$this->_session->getToken().'" />';

        // Because modern browsers don't support anything outside of GET and POST, we need to pass it manually
        if ($this->_method != 'GET' || $this->_method != 'POST')
            $output .= '<input type="hidden" name="_method" value="'.$method.'" />';

        return $this->_fields['_start'] = $output;

    }

    public function close()
    {
        return $this->_fields['_close'] = '</form>';
    }

    /**
     * Generates and stores a new input field, then returns it.
     * 
     * @param string $name
     * @param string $type
     * @param array  $attributes
     * @return string
     */
    public function addInput($name, $type, $default = null, array $attributes)
    {
        $attributes['value'] = $default;

        $output = "<input name=\"{$name}\" type=\"{$type}\" {$this->renderAttributes($attributes)} />";

        return $this->_fields[$name] = $output;
    }

    /**
     * Returns an input string.
     * 
     * @param string $name
     * @param string $type
     * @param array  $attributes
     * @return string
     */
    public function input($name, $type, $default = null, array $attributes)
    {
        return $this->renderWithValue($this->addInput($name, $type, $default = null, $attributes));
    }

    public function addTextarea($name, $default = null, array $attributes)
    {
        $output = "<textarea name=\"{$name}\" 
    }

}