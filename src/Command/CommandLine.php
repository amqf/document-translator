<?php

namespace DocumentTranslator\Command;

use DocumentTranslator\Library\Reader\PDFReader;
use DocumentTranslator\Library\Translator;
use DocumentTranslator\Library\Reader\Transformers\GoogleTranslator;
use Exception;
use Stichoza\GoogleTranslate\GoogleTranslate;
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
    public static function main()
    {
        $flags = Flags::new();

        $flags->addOpt(
            'max-chars-per-request',
            'm',
            'Máximo de caracteres por requisição (padrão: 5000)',
            FlagType::INT
        );

        $flags->addOpt(
            'interval-in-sec',
            'i',
            'Intervalo de espera entre requisições em segundos (padrão: 60)',
            FlagType::INT
        );

        $flags->addOpt(
            'source-filepath',
            's',
            '[OBRIGATÓRIO] Caminho do arquivo (PDF) que será traduzido',
            FlagType::STRING,
            required: true
        );

        $flags->addOpt(
            'output-filepath',
            'o',
            //phpcs:disable
            '[OBRIGATÓRIO] Caminho do arquivo (TXT) que será criado com o conteúdo traduzido',
            FlagType::STRING,
            required: true
        );

        $flags->addOpt(
            'source-lang',
            '',
            //phpcs:disable
            'Idioma do arquivo de origem (padrão: en)',
            FlagType::STRING,
        );

        $flags->addOpt(
            'target-lang',
            '',
            //phpcs:disable
            'Para qual idioma pretende traduzir (padrão: pt-br)',
            FlagType::STRING,
        );

        try {
            $flags->parse();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL . PHP_EOL;

            $flags->displayHelp(true);
            exit(1);
        }

        /**
         * This do setup all script to translate
         * your document as you want.
         *  
         * @var Translator 
         * */
        $transformer = Translator::create(
            PDFReader::create($flags->getOpt('source-filepath')),
            GoogleTranslator::create(
                new GoogleTranslate,
                sourceLang: $flags->getOpt('source-lang', 'en'),
                targetLang: $flags->getOpt('target-lang', 'pt-br')
            ),
            $flags->getOpt('max-chars-per-request', 5000),
            $flags->getOpt('interval-in-sec', 60)
        );

        /**
         * This really use the reader and translator
         * that you setuped early. 
         * */
        $transformer->write(
            $flags->getOpt('output-filepath'),
            onTransform: function (string $old, string $new, int $offset) {
                echo sprintf("Processing offset %d...\n", $offset);
            },
            onSuccess: function (string $filepath) {
                echo sprintf(
                    "Processed %d characters.\n",
                    strlen(file_get_contents($filepath))
                );
                exit(0);
            },
            onError: function (Exception $exception) {
                echo 'ERROR! ' . $exception->getMessage();
                exit(1);
            }
        );
    }
}
