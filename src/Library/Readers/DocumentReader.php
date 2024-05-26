<?php
namespace DocumentTranslator\Library\Readers;

interface DocumentReader
{
    public function setFilepath(string $filepath) : void;
    public function getFilepath() : string;
    public function getText(int $offset=0, int $length = 0) : string;
}