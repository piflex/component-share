<?php

namespace BWC\Share\Image;

class ImageManipulator
{
    const FORMAT_JPG = 'jpg';
    const FORMAT_PNG = 'png';

    /**
     * @var array
     */
    protected $formats;

    public function __construct()
    {
        $this->initializeFormatCallables();
    }

    /**
     * Initialize supported format and their callables
     */
    protected function initializeFormatCallables()
    {
        $this->formats = array(
          self::FORMAT_PNG => 'imagepng',
          self::FORMAT_JPG => 'imagejpeg',
        );
    }

    /**
     * Formats image resource data to chosen format.
     *
     * @param resource $data
     * @param string $format
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function formatImageData($data, $format)
    {
        if (!isset($this->formats[$format])) {
            throw new \InvalidArgumentException(sprintf(
              'Unsupported format %s. Supported foramts are: %s',
              $format,
              implode(', ', array_keys($this->formats))
            ));
        }

        ob_start();
        call_user_func($this->formats[$format], $data);
        $formatted = ob_get_clean();

        return $formatted;
    }
} 