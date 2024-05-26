<?php
// phpcs:disable
namespace DocumentTranslator\Library;

use Exception;
use InvalidArgumentException;
use DocumentTranslator\Library\Reader\PDFReader;
use DocumentTranslator\Library\Translators\Translator;

final class DocumentTranslator
{
    private function __construct(
        private PDFReader $_reader,
        private Translator $_translator,
        private int $_chunk=5000,
        private int $_interval=0
    )
    {
    }

    public function withFile(string $filepath) : self
    {
        $this->_reader->setFile($filepath);
        return $this;
    }

    public function fromLanguage(string $language) : self
    {
        return $this;
    }

    public function toLanguage(string $language) : self
    {
        return $this;
    }

    public function translateChunk(callable $listener) : void
    {
        for (
            $offset = 0;
            $text = $this->_reader->getText($offset, $this->_chunk);
            $offset += $this->_chunk
        )
        {
            $listener(
                $text,
                $this->_translator->translate($text),
                $offset
            );
            
            if($this->_interval != 0)
            {
                sleep($this->_interval);
            }
        }
    }

    /**
     * @param string $filepath
     * @param callable $onTranslate (string $old, string $new, int $offset)
     * @param callable $onSuccess (string $filepath)
     * @param callable $onError (Exception $exception)
     * @return void
     */
    public function translate(
        string $filepath,
        callable $onTranslate = null,
        callable $onSuccess = null,
        callable $onError = null
        ) : void
    {
        $basename = basename($filepath);
        $dirname = dirname($filepath);

        if (empty($basename))
        {
            throw new InvalidArgumentException('invalid filepath');
        }

        if (!empty($dirname) && !is_dir($dirname)) {
            mkdir($dirname, 0755);
        }

        $fp = fopen($filepath, 'a');

        try
        {
            $this->translateChunk(
                function (string $old, string $new, int $offset) use ($fp, $onTranslate) {
                    fwrite($fp, $new);
                    $onTranslate($old, $new, $offset);
                }
            );

            $onSuccess($filepath);
        } catch(Exception $e) {
            $onError($e);
        } 
        
        fclose($fp);
    }

    public static function create(
        PDFReader $reader,
        Translator $translator,
        int $chunk,
        int $interval=0,
    ) : self
    {
        return new self(
            $reader,
            $translator,
            $chunk,
            $interval
        );
    }
}