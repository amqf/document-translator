<?php
// phpcs:disable
namespace DocumentTranslator\Library\Reader;

use Exception;
use InvalidArgumentException;
use DocumentTranslator\Library\Reader\PDFReader;
use DocumentTranslator\Library\Reader\Transformers\Transformer;

final class TransformerManager
{
    private function __construct(
        private PDFReader $_reader,
        private Transformer $_transformer,
        private int $_chunk,
        private int $_interval=0
    )
    {
    }

    public function transform(callable $listener) : void
    {
        for (
            $offset = 0;
            $text = $this->_reader->getText($offset, $this->_chunk);
            $offset += $this->_chunk
        )
        {
            $listener(
                $text,
                $this->_transformer->transform($text),
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
     * @param callable $onTransform (string $old, string $new, int $offset)
     * @param callable $onSuccess (string $filepath)
     * @param callable $onError (Exception $exception)
     * @return void
     */
    public function write(
        string $filepath,
        callable $onTransform = null,
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
            $this->transform(
                function (string $old, string $new, int $offset) use ($fp, $onTransform) {
                    fwrite($fp, $new);
                    $onTransform($old, $new, $offset);
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
        Transformer $transformer,
        int $chunk,
        int $interval=0,
    ) : self
    {
        return new self(
            $reader,
            $transformer,
            $chunk,
            $interval
        );
    }
}