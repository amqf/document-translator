<?php
//phpcs:disable

namespace DocumentTranslator\Library\Reader\Transformers;

use DocumentTranslator\Library\Reader\Transformers\Transformer;
use Stichoza\GoogleTranslate\GoogleTranslate;

final class GoogleTranslator implements Transformer
{
    private function __construct(
        private GoogleTranslate $_googleTranslator,
        private string $_sourceLang = 'en',
        private string $_targetLang = 'pt-br'
    )
    {
        $this->_googleTranslator->setSource($this->_sourceLang);
        $this->_googleTranslator->setTarget($this->_targetLang);
    }

    public function transform(string $text) : string
    {
        return $this->_googleTranslator->translate($text);
    }

    public static function create(
        GoogleTranslate $googleTranslator,
        string $sourceLang = 'en',
        string $targetLang = 'pt-br',
    ) : self
    {
        return new self(
            $googleTranslator,
            $sourceLang,
            $targetLang
        );
    }
}