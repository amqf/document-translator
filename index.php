<?php

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

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

use DocumentTranslator\Reader\PDFReader;
use DocumentTranslator\Reader\TransformerManager;
use DocumentTranslator\Reader\Transformers\GoogleTranslator;
use Stichoza\GoogleTranslate\GoogleTranslate;

require_once './vendor/autoload.php';

use Toolkit\PFlag\Flags;
use Toolkit\PFlag\FlagType;

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
    'Idioma do arquivo de origem',
    FlagType::STRING,
    required: true
);

$flags->addOpt(
    'target-lang',
    '',
    //phpcs:disable
    'Para qual idioma pretende traduzir',
    FlagType::STRING,
    required: true
);

try{
    $flags->parse();
}catch(Exception $e)
{
    echo $e->getMessage() . PHP_EOL . PHP_EOL;

    $flags->displayHelp(true);
    exit(1);
}

define('MAX_CHARS_PER_REQUEST', $flags->getOpt('max-chars-per-request', 5000));
define('INTERVAL_IN_SEC', $flags->getOpt('interval-in-sec', 60));
define('STORAGE_DIR', __DIR__.'/storage');
define('SOURCE_FILEPATH', $flags->getOpt('source-filepath'));
define('OUTPUT_FILEPATH', $flags->getOpt('output-filepath'));
define('SOURCE_LANG', $flags->getOpt('source-lang', 'en'));
define('TARGET_LANG', $flags->getOpt('target-lang', 'pt-br'));

/**
 * This do setup all script to translate
 * your document as you want.
 *  
 * @var TransformerManager 
 * */
$transformer = TransformerManager::create(
    PDFReader::create(SOURCE_FILEPATH),
    GoogleTranslator::create(
        new GoogleTranslate,
        sourceLang: SOURCE_LANG,
        targetLang: TARGET_LANG
    ),
    MAX_CHARS_PER_REQUEST,
    INTERVAL_IN_SEC
);

/**
 * This really use the reader and translator
 * that you setuped early. 
 * */
$transformer->write(
    OUTPUT_FILEPATH,
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