<?php
// phpcs:disable
namespace DocumentTranslator\Library\Readers;

use DocumentTranslator\Core\File;
use Spatie\PdfToText\Pdf;

final class PDFDocumentReader implements DocumentReader
{
    private string $_text;
    private File $_file;

    public function setFile(File $file) : void
    {
        $this->_file = $file;
    }

    public function getFile() : File
    {
        return $this->_file;
    }

    public function getText(int $offset=0, int $length = 0) : string
    {
        if(empty($this->_text))
        {
            $this->_text = Pdf::getText($this->_file->getRealpath());
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