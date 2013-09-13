<?php

namespace m\Foundation;


/**
 * Interface for collection objects.
 * 
 * @package m\Foundation
 */
interface CollectionInterface 
{

    // Setters
    public function set($key, $value);
    public function setMany(array $data);
    public function setAll(array $data);

    // Getters
    public function get($key, $default);
    public function getMany($key);
    public function getAll();

    // Utility
    public function has($key);
    public function keys();

    // Resets
    public function clear($key);
    public function clearAll();

}