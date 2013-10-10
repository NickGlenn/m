<?php

namespace m\Foundation;


/**
 * The core class for m framework.  This is where the meat of the
 * library is stored.
 * 
 * @package m\Foundation
 */
class Core extends Collection
{

    /**
     * @var array Stored hooks
     */
    protected $_hooks = array();

    /**
     * @var array Container
     */
    protected $_ioc = array();

    /**
     * Constructor.
     */
    public function __construct()
    {

        $defaults = array(
            'database'  => array(
                'type'  => 'mysql',
                'host'  => 'localhost',
                'name'  => 'dbname',
                'user'  => 'root',
                'pass'  => ''
            )
        );

    }

    /**
     * Registers a new object container.
     *
     * @param string $name
     * @param \Closure $callable
     * @param bool $singleton
     * @return \m\Foundation\Core
     */
    public function bind($name, \Closure $callable, $singleton = false)
    {
        $this->_ioc[$name] = array(
            'closure'   => $callable,
            'singleton' => (bool) $singleton,
            'instance'  => null
        );

        return $this;
    }

    /**
     * Registers a singleton object container.
     *
     * @param string $name
     * @param \Closure $callable
     * @return \m\Foundation\Core
     */
    public function singleton($name, \Closure $callable)
    {
        return $this->bind($name, $callable, true);
    }

    /**
     * Resolves a set container and returns the result.
     *
     * @param string $name
     * @param array $params
     * @param bool $force
     * @return object|null
     */
    public function make($name, array $params = array(), $force = false)
    {
        if (isset($this->_ioc[$name])) {

            // If a singleton instance exists, return it
            if ($this->_ioc[$name]['instance'] && !$force)
                return $this->_ioc[$name]['instance'];

            // Include an instance of this object as the final argument
            $params[] = $this;

            // Call the stored container
            $result = call_user_func_array($this->_ioc[$name]['closure'], $params);

            // If this is supposed to be a singleton, store it
            if ($this->_ioc[$name]['singleton'])
                $this->_ioc[$name]['instance'] = $result;

            return $result;
        }

        return null;
    }

    /**
     * Force resolves a container and returns the new object.
     *
     * @param string $name
     * @param array $params
     * @return object|null
     */
    public function remake($name, array $params = array())
    {
        return $this->make($name, $params, true);
    }

    /**
     * Calls the hooks stored at the given event name (if any exist) and
     * passes them the given parameters.
     *
     * @param string $event
     * @param array $params
     * @return \m\Foundation\Core
     */
    public function call($event, array $params = array())
    {

        // Include an instance of this object as the final argument
        $params[] = $this;

        if (isset($this->_hooks[$event])) {
            foreach($this->_hooks[$event] as $hook) {
                call_user_func_array($hook, $params);
            }
        }

        return $this;
    }

    /**
     * Binds a new callable action to a hook event.
     *
     * @param string $event
     * @param \Closure|callable $callable
     * @return \m\Foundation\Core
     * @throws \InvalidArgumentException
     */
    public function on($event, $callable)
    {
        if (!is_callable($callable) && !$callable instanceof \Closure)
            throw new \InvalidArgumentException();

        if (!isset($this->_hooks[$event]))
            $this->_hooks[$event] = array();

        $this->_hooks[$event][] = $callable;

        return $this;
    }

}