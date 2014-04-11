<?php

namespace BWC\Share\Image;

class ImageResizer
{
    private $_image;

    public function fromString($image)
    {
        $this->_image = imagecreatefromstring($image);
    }

    public function toString()
    {
        ob_start();
        imagepng($this->_image);
        $contents = ob_get_clean();

        return $contents;
    }

    public function scaleToFit($width, $height)
    {
        if (null === $this->_image) return;

        $rawWidth  = $this->_getWidth();
        $rawHeight = $this->_getHeight();

        $widthOver  = $rawWidth / $width;
        $heightOver = $rawHeight / $height;

        $scalingFactor = max($widthOver, $heightOver);

        if ($scalingFactor > 1) {
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