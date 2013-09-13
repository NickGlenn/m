<?php

namespace m\File;


/**
 * A generic object representation of a file.
 * 
 * @package m\File
 */
class GenericFile implements FileInterface
{

    /**
     * @var string The file contents
     */
    protected $_contents = '';

    /**
     * @var array File path information
     */
    protected $_path;

    /**
     * Constructor.
     *
     * @param string $filepath
     */
    public function __construct($filepath)
    {
        $this->setFilepath($filepath);
    }

    /**
     * Sets the directory, name and extension based on the given
     * filepath string.
     *
     * @param string $filepath
     * @return \m\File\GenericFile
     */
    public function setFilepath($filepath)
    {
        $this->_path = pathinfo($filepath);

        if (!isset($this->_path['extension']))
            $this->_path['extension'] = null;

        return $this;
    }

    /**
     * Returns the full filepath string.
     *
     * @return string
     */
    public function getFilepath()
    {
        $filepath = $this->_path['dirname'].DIRECTORY_SEPARATOR.$this->_path['filename'];

        if ($this->_path['extension'])
            $filepath .= '.'.$this->_path['extension'];

        return $filepath;
    }

    /**
     * Sets the directory for the file.
     *
     * @param $directory
     * @return \m\File\GenericFile
     */
    public function setDirectory($directory)
    {
        $this->_path['dirname'] = (string) rtrim($directory, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Returns the directory for the file.
     *
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->_path['dirname'];
    }

    /**
     * Sets the extension for the file.
     *
     * @param string|null $extension
     * @return \m\File\GenericFile
     */
    public function setExtension($extension = null)
    {
        if (!$extension) {
            $this->_path['extension'] = null;
        } else {
            $this->_path['extension'] = (string) trim($extension, '.');
        }

        return $this;
    }

    /**
     * Returns the extension for the file.
     *
     * @return mixed
     */
    public function getExtension()
    {
        return $this->_path['extension'];
    }

    /**
     * Sets the file name.
     *
     * @param string $name
     * @return \m\File\GenericFile
     */
    public function setName($name)
    {
        $this->_path['filename'] = (string) trim($name, '/\.');

        return $this;
    }

    /**
     * Returns the name of the file.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_path['filename'];
    }

    /**
     * Opens the physical file and loads its contents into the object.
     *
     * @return \m\File\GenericFile
     */
    public function open()
    {
        $loaded = @file_get_contents($this->getFilepath());

        $this->_contents = $loaded ? : '';

        return $this;
    }

    /**
     * Saves the contents to the physical file.
     *
     * @return int|bool
     */
    public function save()
    {
        return file_put_contents($this->getFilepath(), $this->read());
    }

    /**
     * Destroys the physical file.
     *
     * @return \m\File\GenericFile
     */
    public function destroy()
    {
        if (file_exists($this->getFilepath()))
            unlink($this->getFilepath());

        return $this;
    }

    /**
     * Moves the physical file to a new directory.
     *
     * @param string $directory
     * @return int|bool
     */
    public function move($directory)
    {
        return $this->destroy()->setDirectory($directory)->save();
    }

    /**
     * Renames the physical file.
     *
     * @param string $name
     * @return int|bool
     */
    public function change($name)
    {
        return $this->destroy()->setName($name)->save();
    }

    /**
     * Sets the content for the file.
     *
     * @param string $content
     * @return \m\File\GenericFile
     */
    public function write($content)
    {
        $this->_contents = (string) $content;

        return $this;
    }

    /**
     * Appends given content to current file content.
     *
     * @param string $content
     * @return \m\File\GenericFile
     */
    public function append($content)
    {
        $this->_contents .= (string) $content;

        return $this;
    }

    /**
     * Returns the current file content.
     *
     * @return string
     */
    public function read()
    {
        return $this->_contents;
    }

}