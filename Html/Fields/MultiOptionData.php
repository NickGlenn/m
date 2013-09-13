<?php

namespace m\Html\Fields;
use m\Html\Element;

abstract class MultiOptionField extends Element
{

    /**
     * @var array Field options
     */
    protected $_options = array();

    /**
     * Adds an option to the field.  Once added you cannot edit or delete
     * the options.
     *
     * @param string $value
     * @param string|null $display
     * @return \m\Html\Fields\MultiOptionField
     */
    public function addOption($value, $display = null)
    {
        if (null === $display)
            $display = $value;

        $this->_options[] = array($value, $display);

        return $this;
    }

    /**
     * Clears all of the set options.
     *
     * @return \m\Html\Fields\MultiOptionField
     */
    public function clearOptions()
    {
        $this->_options = array();

        return $this;
    }

}