<?php

namespace BWC\Share\Image;

class ImageResizer
{
    private $_image;

    public function fromString($image)
    {
        $this->_image = imagecreatefromstring($image);
        imagealphablending($this->_image, false);
        imagesavealpha($this->_image, true);
    }

    public function toString()
    {
        ob_start();
        imagepng($this->_image);
        $contents = ob_get_clean();

        return $contents;
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool|string $force If true/'force', image will be forced to resize even if it's larger then original
     *                           If 'frame', image will contain a transparent frame if larger then original
     *                           If false, image will not be resized if it's larger than original
     *                             
     */
    public function scaleToFit($width, $height, $force = false)
    {
        $this->scale($width, $height, true, (bool) $force, 'frame' === $force);
    }

    public function scaleToCover($width, $height, $force = false)
    {
        $this->scale($width, $height, false, $force, false);
    }

    /**
     * @param int $width Target width
     * @param int $height Target height
     * @param bool $toFit If true, image fill fit to given dimensions, if false, it will cover them
     * @param bool $force If true, image will be resized even if target dimensions are larger than original.
     * @param bool $frame If true, image will contain a transparent frame if 
     *                    toFit is enabled and it's larger than original
     *                            
     */
    protected function scale($width, $height, $toFit, $force, $frame)
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

        if ($scalingFactor < 1 && $toFit && $force && $frame) {
            $destImage = $this->_createEmptyImage($width, $height);

            imagecopyresampled(
                $destImage, 
                $this->_image,
                ($width - $rawWidth) / 2, 
                ($height - $rawHeight) / 2, 
                0, 
                0, 
                $rawWidth, 
                $rawHeight, 
                $rawWidth, 
                $rawHeight
            );                        

            $this->_image = $destImage;
        } elseif ($scalingFactor > 1 || $force) {
            $destWidth  = $rawWidth / $scalingFactor;
            $destHeight = $rawHeight / $scalingFactor;
            $destImage = $this->_createEmptyImage($destWidth, $destHeight);

            imagecopyresampled($destImage, $this->_image, 0, 0, 0, 0, $destWidth, $destHeight, $rawWidth, $rawHeight);

            $this->_image = $destImage;
        }
    }

    protected function _createEmptyImage($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparent);        

        return $image;
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