<?php
// phpcs:disable
namespace DocumentTranslator\Library\Readers;

use Spatie\PdfToText\Pdf;

final class PDFDocumentReader implements DocumentReader
{
    private string $_text;
    private string $_filepath;

    public function setFilepath(string $filepath) : void
    {
        $this->_filepath = $filepath;
    }

    public function getFilepath() : string
    {
        return $this->_filepath;
    }

    public function getText(int $offset=0, int $length = 0) : string
    {
        if(empty($this->_text))
        {
            $this->_text = Pdf::getText($this->_filepath);
        }

        if ($length === 0) {
            $length = strlen($this->_text);
        }

        return substr($this->_text, $offset, $length);
    }

    public static function create(string $filename) : self
    {
        return new self($filename);
    }
}