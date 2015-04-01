<?php

namespace BWC\Share\Image;

class ImageResizer extends ImageManipulator
{
    /**
     * @var resource
     */
    private $_image;

    public function fromString($image)
    {
        $this->_image = imagecreatefromstring($image);
        imagealphablending($this->_image, false);
        imagesavealpha($this->_image, true);
    }

    public function toString($format = self::FORMAT_PNG, $quality = null)
    {
        return $this->formatImageData($this->_image, $format, $quality);
    }

    public function scaleToFit($width, $height, $force = false)
    {
        $this->scale($width, $height, true, $force);
    }

    public function scaleToCover($width, $height, $force = false)
    {
        $this->scale($width, $height, false, $force);
    }

    /**
     * @param int $width Target width
     * @param int $height Target height
     * @param bool $toFit If true, image fill fit to given dimensions, if false, it will cover them
     * @param bool $force If true, image will be resized even if target dimensions are larger than original
     */
    protected function scale($width, $height, $toFit, $force)
    {
        if (null === $this->_image) return;

        $rawWidth  = $this->_getWidth();
        $rawHeight = $this->_getHeight();

        $widthOver  = $rawWidth / $width;
        $heightOver = $rawHeight / $height;

        if ($toFit) {
            $scalingFactor = max($widthOver, $heightOver);
        } else {
            $scalingFactor = min($widthOver, $heightOver);
        }

        if ($scalingFactor > 1 || $force) {
            $destWidth  = $rawWidth / $scalingFactor;
            $destHeight = $rawHeight / $scalingFactor;

            $destImage = imagecreatetruecolor($destWidth, $destHeight);
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefill($destImage, 0, 0, $transparent);

            imagecopyresampled($destImage, $this->_image, 0, 0, 0, 0, $destWidth, $destHeight, $rawWidth, $rawHeight);

            $this->_image = $destImage;
        }
    }

    protected function _getWidth()
    {
        return $this->_image ? imagesx($this->_image) : null;
    }

    protected function _getHeight()
    {
        return $this->_image ? imagesy($this->_image) : null;
    }
}