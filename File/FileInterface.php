<?php

namespace m\File;


/**
 * Interface for file objects.
 * 
 * @package m\File
 */
interface FileInterface 
{

    // Constructor
    public function __construct($filepath);

    // File location modifiers
    public function setFilepath($filepath);
    public function getFilepath();
    public function setExtension($extension);
    public function getExtension();
    public function setDirectory($directory);
    public function getDirectory();
    public function setName($filename);
    public function getName();

    // Physical file modifiers
    public function open();
    public function save();
    public function move($filepath);
    public function destroy();

}