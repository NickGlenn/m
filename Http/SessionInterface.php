<?php

namespace m\Http;
use m\Foundation\CollectionInterface;


/**
 * Interface for HTTP session objects.
 * 
 * @package m\Http
 */
interface SessionInterface extends CollectionInterface
{

    // Session id
    public function getId();
    public function setId($id = null);

    // Session handling
    public function start();
    public function destroy();
    public function close();

    // Flash getters
    public function getFlashed($key);
    public function getFlashedAll();

    // Flash setters
    public function flash($key, $value);
    public function flashMany(array $data);

    // Flash utilities
    public function getFlashedKeys();
    public function clearFlash();

    // CSRF Token
    public function setToken($token);
    public function getToken();

}