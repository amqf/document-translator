<?php
namespace DocumentTranslator\Library\Readers;

use DocumentTranslator\Core\File;

interface DocumentReader
{
    public function setFile(File $file) : void;
    public function getFile() : File;
    public function getText(int $offset=0, int $length = 0) : string;
}