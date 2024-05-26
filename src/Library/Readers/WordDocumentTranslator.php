<?php
// phpcs:disable
namespace DocumentTranslator\Library\Readers;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;

final class WordDocumentTranslator implements DocumentReader
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
        /**
         * TODO: não armazenar todo o texto em memória para otimizar seu uso 
        */
        if(empty($this->_text))
        {
            $phpWord = IOFactory::load($this->_filepath);

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