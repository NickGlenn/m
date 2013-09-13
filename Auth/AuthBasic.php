<?php

namespace m\Auth;
use m\Http\SessionInterface;

/**
 * A very basic authentication layer.
 * 
 * @package m\Auth
 */
class AuthBasic
{

    /**
     * @var \m\Http\SessionInterface The session object
     */
    protected $_session;
    
    /**
     * Constructor.
     * 
     * @param \m\Http\SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->_session = $session;
    }
 
    /**
     * Returns true if the user is logged in.
     * 
     * @return type
     */
    public function isLoggedIn()
    {
        return $this->_session->get('user') ? true : false;
    }
    
    /**
     * Returns true if the user is not logged in.
     * 
     * @return bool
     */
    public function isGuest()
    {
        return !$this->_session->get('user') ? true : false;
    }
 
    /**
     * Stores an id, name and password for the user session.
     * 
     * @param int $id
     * @param string $name
     * @param string $password
     * @return \m\Auth\AuthBasic
     */
    public function login($id, $name, $password)
    {
        $this->_session->set('user', array(
            'id'        => $id,
            'name'      => $name,
            'password'  => $password
        ));
        
        return $this;
    }
    
    /**
     * Log the user out without destroying the session.
     * 
     * @return \m\Auth\AuthBasic
     */
    public function logout()
    {
        $this->_session->clear('user');
        
        return $this;
    }
    
}