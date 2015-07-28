<?php

namespace citibytes;

use citibytes\exceptions\WatermarkException;

class Watermark
{

  private static $_WATERMARK_IMAGE_PATH;

	public function __construct()
	{	
		$this->_WATERMARK_IMAGE_PATH = ROOT_DIRECTORY . "/images/watermark.png";
		
		if(extension_loaded('imagick') === FALSE)
			throw new WatermarkException("Imagick module not available in server"); 

		if(file_exists($this->_WATERMARK_IMAGE_PATH) === FALSE)
    	throw new WatermarkException("Watermark image unavailable");	
	
	}

	public function addWatermark($image_path)
	{
		try{
			// Open the original image
			$image			= new \Imagick();
			$is_success = $image->readImage($image_path);

			if($is_success === FALSE)
				throw new WatermarkException("Cannot read uploaded image");

			$watermark 	= new \Imagick();
			$is_success	=	$watermark->readImage($this->_WATERMARK_IMAGE_PATH);

			if($is_success === FALSE)
				throw new WatermarkException("Cannot read uploaded image");

			$image_width = $image->getImageWidth();
			$image_height= $image->getImageHeight();

			$watermark_width = $watermark->getImageWidth();
			$watermark_height= $watermark->getImageHeight();

			$watermark_pos_x = $image_width - $watermark_width - 20;
			$watermark_pos_y = 20;

			// Overlay the watermark on the original image
			$is_success	=	$image->compositeImage($watermark, \imagick::COMPOSITE_OVER,
																					 $watermark_pos_x, $watermark_pos_y);

			if($is_success === FALSE)
				throw new WatermarkException("Cannot save image after watermarked");

			//Write the image
			$image->writeImage($image_path);
		}catch(ImagickException $e){
  		error_log($e->getMessage());
			throw new WatermarkException($e->getMessage(),$e->getCode(),$e);
		}

	}


}


?>
