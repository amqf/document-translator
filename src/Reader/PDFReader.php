<?php
// phpcs:disable
namespace DocumentTranslator\Reader;

use Spatie\PdfToText\Pdf;

final class PDFReader
{
    private string $_text;

    private function __construct(
        private string $_filepath
    )
    {
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