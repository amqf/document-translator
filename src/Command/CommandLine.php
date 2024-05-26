<?php

namespace DocumentTranslator\Command;

use DocumentTranslator\Library\DocumentTranslator;
use DocumentTranslator\Library\Reader\PDFReader;
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
            'file',
            'f',
            '[OBRIGATÓRIO] Caminho do arquivo (PDF) que será traduzido',
            FlagType::STRING,
            required: true
        );

        $flags->addOpt(
            'output',
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

        $file = realpath($flags->getOpt('file'));
        $filepath = realpath($flags->getOpt('output'));
        $basename = basename($filepath);
        $dirname = dirname($filepath);

        if (empty($basename))
        {
            throw new InvalidArgumentException('Invalid filepath: ' . $filepath);
        }

        if (!empty($dirname) && !is_dir($dirname)) {
            mkdir($dirname, 0755);
        }

        $fp = fopen($filepath, 'a');

        DocumentTranslator::create(
            new PDFReader(),
            new GoogleTranslator,
            chunk: 5000
        )->withFile($file)
        ->fromLanguage($flags->getOpt('source-lang', 'en'))
        ->toLanguage($flags->getOpt('target-lang', 'pt-br'))
        ->translate(
            onTranslate: function (string $old, string $new, int $offset) use ($fp) {
                echo sprintf("Processing offset %d...\n", $offset);
                fwrite($fp, $new);
            },
            onSuccess: function (string $filepath) {
                echo sprintf(
                    "Processed %d characters.\n",
                    strlen(file_get_contents($filepath))
                );
                exit(0);
            },
            onError: function (Exception $exception) use ($fp) {
                echo 'ERROR! ' . $exception->getMessage();
                fclose($fp);
                exit(1);
            }
        );

        fclose($fp);
    }
}
