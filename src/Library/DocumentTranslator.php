<?php
// phpcs:disable
namespace DocumentTranslator\Library;

use DocumentTranslator\Core\File;
use Exception;
use DocumentTranslator\Library\Readers\DocumentReader;
use DocumentTranslator\Library\Translators\Translator;
use InvalidArgumentException;

final class DocumentTranslator
{
    private int $_amountTranslatedChars = 0;
    private int $_chunk;
    private int $_interval;

    private function __construct(
        private DocumentReader $_reader,
        private Translator $_translator,
        
    )
    {
        $this->_chunk = 5000;
        $this->_interval = 0;
    }

    public function withFile(File $file) : self
    {
        $this->_reader->setFile($file);
        return $this;
    }

    public function setChunk(int $chunk) : void
    {
        if($chunk < 10)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'DocumentTranslator chunk cannot be less than 10. Given %d',
                    $chunk
                )
            );
        }

        $this->_chunk = $chunk;
    }

    public function setInterval(int $interval) : void
    {
        if($interval < 0)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'DocumentTranslator interval cannot be less than 0. Given %d',
                    $interval
                )
            );
        }

        $this->_interval = $interval;
    }

    public function fromLanguage(string $language) : self
    {
        if($language < 0)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'DocumentTranslator fromLanguage cannot empty',
                    $language
                )
            );
        }

        return $this;
    }

    public function toLanguage(string $language) : self
    {
        if($language < 0)
        {
            throw new InvalidArgumentException(
                sprintf(
                    'DocumentTranslator toLanguage cannot empty',
                    $language
                )
            );
        }

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
        DocumentReader $reader,
        Translator $translator
    ) : self
    {
        return new self(
            $reader,
            $translator
        );
    }
}