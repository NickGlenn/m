<?php

namespace m\Validation\Handlers;
use m\Validation\HandlerAbstract;


class MinimumHandler extends HandlerAbstract
{

    /**
     * Returns true if the number of characters is greater than the required minimum.
     *
     * @param string $input
     * @param integer $min
     * @return bool
     */
    public function checkString($input, $min)
    {
        return strlen($input) >= (integer) $min;
    }

    /**
     * Returns true if the input is greater than the required minimum.
     *
     * @param float|int $input
     * @param integer $min
     * @return bool
     */
    public function checkNumeric($input, $min)
    {
        return $input >= (integer) $min;
    }

    /**
     * Returns true if the input array has more items than the required minimum.
     *
     * @param array $input
     * @param integer $min
     * @return bool
     */
    public function checkArray($input, $min)
    {
        return count($input) >= (integer) $min;
    }

    /**
     * Returns true if the input filesize (in bytes) is greater than the required minimum.
     *
     * @param array $input
     * @param integer $min
     * @return bool
     */
    public function checkFile($input, $min)
    {
        return $input['size'] >= (integer) $min;
    }

    /**
     * Formats the message.
     *
     * @param string $message
     * @param string $key
     * @param array $params
     * @return string|void
     */
    public function formatMessage($message, $key, array $params)
    {
        return str_replace(
            array(':key', ':min'),
            array($key, $params[0]),
            $message
        );
    }

}