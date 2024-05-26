<?php

namespace DocumentTranslator\Core;

use DocumentTranslator\Core\File;

final class Arguments
{
    private function __construct(
        public File $inputFile,
        public File $outputFile,
        public string $fromLanguage,
        public string $toLanguage,
        public string $chunk,
        public string $interval,
    )
    {
    }

    public static function create(
        string $inputFile,
        string $outputFile,
        string $fromLanguage,
        string $toLanguage,
        string $chunk,
        string $interval,
    ) : self
    {
        return new self(
            new File($inputFile),
            new File($outputFile),
            $fromLanguage,
            $toLanguage,
            $chunk,
            $interval,
        );
    }
}