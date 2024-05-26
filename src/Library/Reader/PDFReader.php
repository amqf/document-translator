<?php
// phpcs:disable
namespace DocumentTranslator\Library\Reader;

use Spatie\PdfToText\Pdf;

final class PDFReader
{
    private string $_text;
    private string $_filename;

    public function setFile(string $filepath)
    {
        $this->_filepath = $filepath;
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