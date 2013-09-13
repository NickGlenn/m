<?php

namespace m\Validation;


abstract class HandlerAbstract
{

    /**
     * Checks a string input.
     *
     * @param $input
     * @return bool
     */
    public function checkString($input)
    {
        return true;
    }

    /**
     * Checks a numeric input.
     *
     * @param integer|float $input
     * @return bool
     */
    public function checkNumeric($input)
    {
        return true;
    }

    /**
     * Checks an array input.
     *
     * @param array $input
     * @return bool
     */
    public function checkArray(array $input)
    {
        return true;
    }

    /**
     * Check a file array input.
     *
     * @param array $input
     * @return bool
     */
    public function checkFile(array $input)
    {
        return true;
    }

    /**
     * Formats a message string.
     *
     * @param string $message
     * @param string $key
     * @param array $params
     * @return string
     */
    public function formatMessage($message, $key, array $params = array())
    {
        return str_replace(':key', $key, $message);
    }

}