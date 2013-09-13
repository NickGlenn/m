<?php

namespace m;


class m
{

    /**
     * @var \m\Core Stored Core instance
     */
    protected static $instance;

    /**
     * Private access constructor.
     */
    private function __construct()
    {
    }

    /**
     * Sets a custom Core object instance.
     *
     * @param \m\Foundation\Core $core
     */
    public function setInstance(Foundation\Core $core)
    {
        static::$instance = $core;
    }

    /**
     * Get stored Core object instance.
     *
     * @return \m\Foundation\Core
     */
    public static function getInstance()
    {
        if (!static::$instance)
            static::$instance = new Foundation\Core();

        return static::$instance;
    }

    /**
     * Access object methods of stored Core class instance.
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {

        // Reduce the function calls
        $instance = static::getInstance();

        // If a matching Core method exists
        if (method_exists($instance, $name))
            return call_user_func_array(array($instance, $name), $args);

        // Otherwise, act as a shortcut to resolve a registered function
        return $instance->make($name, $args);
    }

    /**
     * Simple helper class for accessing the $_GET array.  Returns
     * the requested key if it exists, otherwise it returns the given
     * default value.  If no key is provided, it returns the $_GET array.
     *
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    public static function query($key = null, $default = null)
    {
        if (null === $key)
            return $_GET;

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Simple helper class for accessing the $_POST array.  Returns
     * the requested key if it exists, otherwise it returns the given
     * default value.  If no key is provided, it returns the $_POST array.
     *
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    public static function post($key = null, $default = null)
    {
        if (null === $key)
            return $_POST;

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

}