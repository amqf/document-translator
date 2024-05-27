<?php

namespace DocumentTranslator\Command;

use DocumentTranslator\Core\Arguments;
use DocumentTranslator\Library\DocumentTranslator;
use DocumentTranslator\Library\Readers\PDFDocumentReader;
use DocumentTranslator\Library\Translators\GoogleTranslator;
use Exception;
use InvalidArgumentException;
use Toolkit\PFlag\Flags;
use Toolkit\PFlag\FlagType;

/**
 * PHP Version ^8
 * 
 * Used in command line for translate documents
 * from a language to another.
 * 
 * @category Utility
 * @package  DocumentTranslator
 * @author   Antônio M. Quadros Filho <antoniomquadrosfilho@gmail.com>
 * @license  [7](http://www.php.net/license/3_01.txt)  Licença PHP 3.01
 * @link     [8](https://packagist.org/packages/amqf/document-translator)
 */
final class CommandLine
{
    /**
     * REQUIRED ARGUMENTS
     */
    private const REQUIRED_ARGUMENTS = [
        [
            'name' => 'file',
            'desc' => '[OBRIGATÓRIO] Caminho do arquivo (PDF) que será traduzido',
            'type' => FlagType::STRING,
        ],
        [
            'name' => 'output',
            'desc' => '[OBRIGATÓRIO] Caminho do arquivo (TXT) que será criado com o conteúdo traduzido',
            'type' => FlagType::STRING,
        ],
    ];

    /**
     * OPTIONAL ARGUMENTS
     */
    private const OPTIONAL_ARGUMENTS = [
        [
            'name' => 'interval',
            'shortcut' => 'i',
            'desc' => 'Intervalo de espera entre requisições em segundos (padrão: 60)',
            'type' => FlagType::INT,
        ],
        [
            'name' => 'from',
            'shortcut' => '',
            'desc' => 'Idioma do arquivo de origem (padrão: en)',
            'type' => FlagType::STRING,
        ],
        [
            'name' => 'to',
            'shortcut' => '',
            'desc' => 'Para qual idioma pretende traduzir (padrão: pt-br)',
            'type' => FlagType::STRING,
        ],
        [
            'name' => 'chunk',
            'shortcut' => '',
            'desc' => 'Quantidade de caracteres traduzido por requisição (padrão: 5000)',
            'type' => FlagType::INT,
        ],
    ];

    public static function main()
    {
        /** @var Arguments $arguments */
        $arguments = static::parseArguments();

        if (!$arguments->inputFile->exists())
        {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file path: %s',
                    $arguments->inputFile->getPath()
                ),
            );
        }

        $fp = fopen($arguments->outputFile->getPath(), 'a');

        DocumentTranslator::create(
            match ($arguments->inputFile->getExtension()) {
                'pdf' => new PDFDocumentReader,
            },
            new GoogleTranslator
        )->withFile($arguments->inputFile)
        ->fromLanguage($arguments->fromLanguage)
        ->toLanguage($arguments->toLanguage)
        ->translate(
            onTranslate: function (string $old, string $new, int $offset) use ($fp) {
                echo sprintf("Processing offset %d...\n", $offset);
                fwrite($fp, $new);
            },
            onSuccess: function (string $amountTranslatedChars) use ($fp) {
                echo sprintf(
                    "X Processed %d characters.\n",
                    $amountTranslatedChars
                );
                fclose($fp);
                exit(0);
            },
            onError: function (Exception $exception) use ($fp) {
                echo 'ERROR! ' . $exception->getMessage();
                fclose($fp);
                exit(1);
            }
        );
    }

    private static function parseArguments() : Arguments
    {
        $flags = Flags::new();

        foreach(static::REQUIRED_ARGUMENTS as $argument)
        {
            $flags->addArg(...$argument);
        }

        foreach(static::OPTIONAL_ARGUMENTS as $option)
        {
            $flags->addOpt(...$option);
        }

        try {
            return (ArgumentParser::parser(
                $flags,
                defaultFrom: 'en',
                defaultTo: 'pt-br',
                defaultChunk: 5000,
                defaultInterval: 60
            ));
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL . PHP_EOL;
            $flags->displayHelp(true);
            exit(1);
        }
    }
}
