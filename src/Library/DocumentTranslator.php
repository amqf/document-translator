<?php
// phpcs:disable
namespace DocumentTranslator\Library;

use Exception;
use DocumentTranslator\Library\Reader\PDFReader;
use DocumentTranslator\Library\Translators\Translator;

final class DocumentTranslator
{
    private int $_amountTranslatedChars = 0;
    
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
        $this->_reader->setFilepath($filepath);
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

    public function translateChunks(callable $listener) : void
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
        callable $onTranslate = null,
        callable $onSuccess = null,
        callable $onError = null
        ) : void
    {
        try
        {
            $this->translateChunks(
                function (string $old, string $new, int $offset) use ($onSuccess, $onTranslate) {
                    $onTranslate($old, $new, $offset);
                    $this->increaseAmountTranslatedChars($old);
                }
            );

            $onSuccess($this->getAmountTranslatedChars());
            
        } catch(Exception $e) {
            $onError($e);
        }        
    }

    private function increaseAmountTranslatedChars(string $text) : void
    {
        $this->_amountTranslatedChars += strlen($text);
    }

    private function getAmountTranslatedChars() : int
    {
        return $this->_amountTranslatedChars;
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