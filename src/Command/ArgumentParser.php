<?php

namespace DocumentTranslator\Command;

use DocumentTranslator\Core\Arguments;
use DocumentTranslator\Core\File;
use Toolkit\PFlag\Flags;

final class ArgumentParser
{
    public static function parser(
        Flags $flags,
        string $defaultFrom = 'en',
        string $defaultTo = 'pt-br',
        int $defaultChunk = 5000,
        int $defaultInterval = 60
    ) : Arguments
    {
        $flags->parse();
        
        return Arguments::create(
            $flags->getArg('file'),
            $flags->getArg('output'),
            $flags->getOpt('from', $defaultFrom),
            $flags->getOpt('to', $defaultTo),
            $flags->getOpt('chunk', $defaultChunk),
            $flags->getOpt('interval', $defaultInterval),
        );
    }
}