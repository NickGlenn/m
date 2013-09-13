<?php

namespace m\Http;


/**
 * An object representation of the session.
 * 
 * @package m\Http
 */
class Session implements SessionInterface
{

    /**
     * Starts a new session.
     *
     * @param string $id
     */
    public function __construct($id = null) {
        $this->start($id);

        // Cycle the flash messages
        $_SESSION['m_flash'] = isset($_SESSION['m_flash_next']) ? $_SESSION['m_flash_next'] : array();

        // Prepare for the next page flash
        $_SESSION['m_flash_next'] = array();

    }

    /**
     * Returns the current session id.
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Sets the new session id.
     *
     * @param string|null $session_id
     * @return \m\Http\Session
     */
    public function setId($id = null)
    {
        session_id($id);

        return $this;
    }

    /**
     * Starts the session if the session is not started.
     *
     * @param string|null $id
     * @return \m\Http\Session
     */
    public function start($id = null)
    {
        if (!session_id())
            session_start($id);

        return $this;
    }


    /**
     * Destroys the entire session.
     *
     * @return \m\Http\Session
     */
    public function destroy($offset = null) {
        session_destroy();

        return $this;
    }

    /**
     * Close session and write all data to file
     *
     * @return \m\Http\Session
     */
    public function close() {
        session_write_close();

        return $this;
    }

    /**
     * Set an item in the session.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Http\Session
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Merge an array of items with the current session data.
     *
     * @param array $data
     * @return \m\Http\Session
     */
    public function setMany(array $data)
    {
        $_SESSION = array_merge($_SESSION, $data);

        return $this;
    }

    /**
     * Sets all of the session data.
     *
     * @param array $data
     * @return \m\Http\Session
     */
    public function setAll(array $data)
    {
        $_SESSION = $data;

        return $this;
    }

    /**
     * Returns an item from the session data or returns the given default
     * if the requested item does not exist.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Returns an array containing any of the requested items found.
     *
     * @param string|array $keys
     * @return array
     */
    public function getMany($keys)
    {
        if (!is_array($keys))
            $keys = func_get_args();

        $newArray = array();

        foreach($keys as $key) {
            if (isset($_SESSION[$key])) {
                $newArray[$key] = $_SESSION[$key];
            }
        }

        return $newArray;
    }

    /**
     * Returns the entire session data array.
     *
     * @return array
     */
    public function getAll()
    {
        return $_SESSION;
    }

    /**
     * Returns true if the given key(s) are stored in the session data.
     *
     * @param string|array $key
     * @return bool
     */
    public function has($key)
    {
        $keys = (func_num_args() > 1) ? func_get_args() : (array) $key;

        foreach ($keys as $key) {
            if(!array_key_exists($key, $_SESSION))
                return false;
        }

        return true;
    }

    /**
     * Returns an array containing all of the session data keys.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($_SESSION);
    }

    /**
     * Clears a single item from the session data.
     *
     * @param string $key
     * @return \m\Http\Session
     */
    public function clear($key)
    {
        unset($_SESSION[$key]);

        return $this;
    }

    /**
     * Resets all of the session data.
     *
     * @return \m\Http\Session
     */
    public function clearAll()
    {

        $_SESSION = array(
            'm_flash_next'  => array(),
            'm_flash'       => array(),
            'm_csrf_token'  => $this->getToken()
        );

        return $this;
    }

    /**
     * Returns an item flashed from the previous page or returns
     * the given default if the requested flash message doesn't exist.
     *
     * @param string $key
     * @param mixed $default
     * @return bool
     */
    public function getFlashed($key, $default = null)
    {
        return isset($_SESSION['m_flash'][$key]) ? $_SESSION['m_flash'][$key] : $default;
    }

    /**
     * Returns all of the flashed data from the previous page.
     *
     * @return mixed
     */
    public function getFlashedAll()
    {
        return $_SESSION['m_flash'];
    }

    /**
     * Returns an array containing the keys for the flash messages
     * from the previous page.
     *
     * @return array
     */
    public function getFlashedKeys()
    {
        return array_keys($_SESSION['m_flash']);
    }

    /**
     * Flash a message for the next page.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Http\Session
     */
    public function flash($key, $value)
    {
        $_SESSION['m_flash_next'][$key] = $value;

        return $this;
    }

    /**
     * Merge given data with current flash array for the next page.
     *
     * @param array $data
     * @return \m\Http\Session
     */
    public function flashMany(array $data)
    {
        $_SESSION['m_flash_next'] = array_merge($_SESSION['m_flash_next'], $data);

        return $this;
    }

    /**
     * Clears all flashed messages for the next page.
     *
     * @return \m\Http\Session
     */
    public function clearFlash()
    {
        $_SESSION['m_flash_next'] = array();

        return $this;
    }

    /**
     * Sets the CSRF token for the session.
     *
     * @param string $token
     * @return \m\Http\Session
     */
    public function setToken($token)
    {
        $_SESSION['m_csrf_token'] = $token;

        return $this;
    }

    /**
     * Returns the set CSRF token for the session.
     *
     * @return string
     */
    public function getToken()
    {
        if (!isset($_SESSION['m_csrf_token']))
            $_SESSION['m_csrf_token'] = $this->makeToken();

        return $_SESSION['m_csrf_token'];
    }

    /**
     * Makes and returns a token based on the user IP and a random number.
     *
     * @return string
     */
    public function makeToken()
    {
        return md5($_SERVER['REMOTE_ADDR'].'-'.rand(1000, 9999));
    }

}