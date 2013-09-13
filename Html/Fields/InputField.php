<?php

namespace m\Html\Fields;
use m\Html\Element;


class InputField extends Element
{

    /**
     * @var array Field attributes
     */
    protected $_attr = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param array $attr
     */
    public function __construct($name, $type, array $attr = array())
    {
        $attr['name']   = (string) $name;
        $attr['type']   = (string) $type;

        $this->_attr = $attr;
    }

    /**
     * Compiles the field to a string and returns it.
     *
     * @return string
     */
    public function render()
    {
        return '<input '.$this->renderAttributes().' />';
    }

}