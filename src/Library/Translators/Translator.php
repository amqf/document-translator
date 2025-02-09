<?php
// phpcs:disable
namespace DocumentTranslator\Library\Translators;

interface Translator
{
    public function translate(string $text) : string;
    public function fromLanguage(string $language) : self;
    public function toLanguage(string $language) : self;
}