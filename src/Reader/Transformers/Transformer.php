<?php
// phpcs:disable
namespace DocumentTranslator\Reader\Transformers;

interface Transformer
{
    public function transform(string $text) : string;
}