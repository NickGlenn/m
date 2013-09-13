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
     * @var string The form action
     */
    protected $_action;

    /**
     * @var array The form fields
     */
    protected $_fields = array();

    /**
     * @var string The form method
     */
    protected $_method;

    /**
     * @var bool Support multipart encoding
     */
    protected $_multipart;

    /**
     * @var \m\Http\SessionInterface Session object
     */
    protected $_session;

    /**
     * Constructor.
     *
     * @param string $action
     * @param string $method
     * @param bool $multipart
     */
    public function __construct($action = '', $method = 'POST', $multipart = false)
    {
        $this->_action = (string) $action;
        $this->_method = (string) $method;
        $this->_multipart = (bool) $multipart;
    }

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
     * Sets the action for the form.
     *
     * @param string $action
     * @return \m\Html\Form
     */
    public function setAction($action)
    {
        $this->_action = (string) $action;

        return $this;
    }

    /**
     * Returns the action for the form.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Sets the method for the form.
     *
     * @param string $method
     * @return \m\Html\Form
     */
    public function setMethod($method)
    {
        $this->_method = (string) $method;

        return $this;
    }

    /**
     * Returns the method for the form.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Enables/disable multipart encoding.
     *
     * @param bool $enable
     * @return \m\Html\Form
     */
    public function useMultipart($enable = true)
    {
        $this->_multipart = (bool) $enable;

        return $this;
    }

    /**
     * Returns true if multipart encoding is enabled.
     *
     * @return bool
     */
    public function isMultipart()
    {
        return $this->_multipart;
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
        $output =   '<form action="'.$this->_action.'" method="'.$this->_method.'"'.
                    ($this->_multipart ? 'enctype="multipart/form-data"' : '').' />';

        if ($this->_session)
            $output .= '<input type="hidden" name="csrf_token" value="'.$this->_session->getToken().'" />';

        foreach($this->_fields as $field) {

            $output .= $field->render();

        }

        return $output.'</form>';
    }

    /**
     * Updates the values of the stored fields.
     *
     * @param array $data
     * @return \m\Html\Form
     */
    public function update(array $data)
    {
        foreach($data as $name => $value) {

            if (isset($this->_fields[$name]))
                $this->_fields[$name]->setValue($value);

        }

        return $this;
    }

}