<?php
// phpcs:disable
namespace DocumentTranslator\Library\Reader\Transformers;

interface Transformer
{
    public function transform(string $text) : string;
}