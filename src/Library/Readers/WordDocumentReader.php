<?php
// phpcs:disable
namespace DocumentTranslator\Library\Readers;

use DocumentTranslator\Core\File;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;

final class WordDocumentReader implements DocumentReader
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
        /**
         * TODO: não armazenar todo o texto em memória para otimizar seu uso 
        */
        if(empty($this->_text))
        {
            $phpWord = IOFactory::load($this->_file->getRealpath());

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof TextRun) {
                        $this->_text .= $element->getText();
                    }
                }
            }
        }

        if ($length === 0) {
            $length = strlen($this->_text);
        }

        return substr($this->_text, $offset, $length);
    }
}