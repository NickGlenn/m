<?php

namespace m\Validation\Handlers;
use m\Validation\HandlerAbstract;


class EmailHandler extends HandlerAbstract
{

    /**
     * Returns true if the input is a valid email address.
     *
     * @param string $input
     * @return bool
     */
    public function checkString($input)
    {
        return preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $input) ? true : false;
    }

}