#!/usr/bin/env php
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
// Obtenha o caminho absoluto para o autoloader do Composer
require_once './vendor/autoload.php';

use DocumentTranslator\Command\CommandLine;

CommandLine::main();