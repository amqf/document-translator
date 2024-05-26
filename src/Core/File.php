<?php

namespace DocumentTranslator\Core;

use InvalidArgumentException;

final class File
{
    private string $_dirname;
    private string $_basename;
    private string $_extension;
    private string $_filename;

    public function __construct(private string $_filepath)
    {
        if(empty($this->_filepath))
        {
            throw new InvalidArgumentException('Filepath cannot be empty');
        }

        $this->_dirname = pathinfo($_filepath, PATHINFO_DIRNAME);
        $this->_basename = pathinfo($_filepath, PATHINFO_BASENAME);
        $this->_extension = pathinfo($_filepath, PATHINFO_EXTENSION);
        $this->_filename = pathinfo($_filepath, PATHINFO_FILENAME);
    }

    public function getPath() : string
    {
        return $this->_filepath;
    }

    public function getRealpath() : string
    {
        return realpath($this->_filepath);
    }

    public function getExtension() : string
    {
        return $this->_extension;
    }

    public function getDirname() : string
    {
        return $this->_dirname;
    }

    /**
     * Name without extension
     */
    public function getName() : string
    {
        return $this->_filename;
    }

    /**
     * Name with extension
     */
    public function getBasename() : string
    {
        return $this->_basename;
    }

    /**
     * Check if file exists with file_exists
     */
    public function exists() : bool
    {
        return file_exists($this->_filepath);
    }

    public function toArray()
    {
        return [
            $this->exists(),
            $this->getPath(),
            $this->getRealpath(),
            $this->getExtension(),
            $this->getDirname(),
            $this->getName(),
            $this->getBasename(),
        ];
    }
}