<?php
//phpcs:disable

namespace DocumentTranslator\Library\Reader\Translators;

use Stichoza\GoogleTranslate\GoogleTranslate;

final class GoogleTranslator implements Translator
{
    private GoogleTranslate $_googleTranslator;
    private string $_sourceLang = 'en';
    private string $_targetLang = 'pt-br';

    public function __construct()
    {
        $this->_googleTranslator = new GoogleTranslate(); 
        $this->_googleTranslator->setSource($this->_sourceLang);
        $this->_googleTranslator->setTarget($this->_targetLang);
    }

    public function fromLanguage(string $language): self
    {
        $this->_googleTranslator->setSource($language);
        return $this;
    }

    public function toLanguage(string $language): self
    {
        $this->_googleTranslator->setTarget($language);
        return $this;
    }

    public function translate(string $text) : string
    {
        return 'xablauzim';
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