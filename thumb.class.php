<?php
/**
 *	[公众微信智能云平台(cloud_wx.{})] (C)2013-2099 Powered by YangLin.
 * Author QQ:28945763  问题解答技术交流QQ群：294440459
 *	Version: 5.5       http://i.binguo.me
 *	Date: 2013-4-4 00:00
 */
/**
 * 缩略图生成类(GD Lib required)
 *
 * @author fising <fising@qq.com>
 * @version V1.0.0
 */
class Thumbnail
{
	/**
	 * 缩放比例参数
	 *
	 * @var number
	 */
	private $_percent;

	/**
	 * 缩略图最大宽度(像素)
	 *
	 * @var integer
	 */
	private $_maxWidth;

	/**
	 * 缩略图最大高度(像素)
	 *
	 * @var integer
	 */
	private $_maxHeight;

	/**
	 * 原图路径
	 *
	 * @var string
	 */
	private $_sourceImage;

	/**
	 * 原图的宽度
	 *
	 * @var integer
	 */
	private $_sourceImageWidth;

	/**
	 * 原图的高度
	 *
	 * @var integer
	 */
	private $_sourceImageHeight;

	/**
	 * 原图片的类型
	 *
	 * @var string
	 */
	private $_sourceImageType;

	/**
	 * 目标图片资源
	 *
	 * @var source
	 */
	private $_destImage;

	/**
	 * 目标图片的宽度
	 *
	 * @var integer
	 */
	private $_destImageWidth;

	/**
	 * 目标图片的高度
	 *
	 * @var integer
	 */
	private $_destImageHeight;

	/**
	 * 目标图片的文件类型
	 *
	 * @var string
	 */
	private $_destImageType;

	/**
	 * 允许的文件类型
	 *
	 * @var array
	 */
	public static $ALLOW_FILE_TYPES = array('jpeg', 'gif', 'png');

	/**
	 * 构造函数
	 *
	 * @param number $percent 缩放比例参数
	 * @param integer $maxWidth 缩略图最大宽度
	 * @param integer $maxHeight 缩略图最大高度
	 */
	public function __construct($percent = 1, $maxWidth = null, $maxHeight = null)
	{
		try
		{
			if(is_numeric($percent) && $percent)
				$this->_percent = $percent;
			else
				throw new Exception('Invalid percent argument. (' . __FILE__ . ':' . __LINE__ . ')');

			if((is_int($maxWidth) && $maxWidth) || is_null($maxWidth))
				$this->_maxWidth = $maxWidth;
			else
				throw new Exception('Invalid maxWidth argument. (' . __FILE__ . ':' . __LINE__ . ')');

			if((is_int($maxHeight) && $maxHeight) || is_null($maxHeight))
				$this->_maxHeight = $maxHeight;
			else
				throw new Exception('Invalid maxHeight argument. (' . __FILE__ . ':' . __LINE__ . ')');
		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
		}
	}

	/**
	 * 构造函数(兼容PHP4)
	 *
	 * @param number $percent 缩放比例参数
	 * @param integer $maxWidth 缩略图最大宽度
	 * @param integer $maxHeight 缩略图最大高度
	 */
	public function Thumbnail($percent = 1, $maxWidth = null, $maxHeight = null)
	{
		$this->__construct($percent, $maxWidth, $maxHeight);
	}

	/**
	 * 设置缩放比例
	 *
	 * @param number $percent 缩放比例
	 */
	public function setPercent($percent = 1)
	{
		try
		{
			if(is_numeric($percent) && $percent)
				$this->_percent = $percent;
			else
				throw new Exception('Invalid percent argument');
		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
		}
	}

	/**
	 * 设置图片的最大宽度
	 *
	 * @param integer $maxWidth 缩略图最大宽度
	 */
	public function setMaxWidth($maxWidth = null)
	{
		try
		{
			if((is_int($maxWidth) && $maxWidth) || is_null($maxWidth))
				$this->_maxWidth = $maxWidth;
			else
				throw new Exception('Invalid maxWidth argument. (' . __FILE__ . ':' . __LINE__ . ')');
		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
		}
	}

	/**
	 * 设置图片的最大高度
	 *
	 * @param integer $maxHeight 缩略图最大高度
	 */
	public function setMaxHeight($maxHeight = null)
	{
		try
		{
			if((is_int($maxHeight) && $maxHeight) || is_null($maxHeight))
				$this->_maxHeight = $maxHeight;
			else
				throw new Exception('Invalid maxWidth argument. (' . __FILE__ . ':' . __LINE__ . ')');
		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
		}
	}

	/**
	 * 生成缩略图
	 *
	 * @param string $sourceImagePath 原图路径
	 * @param string $destImagePath 生成的缩略图路径
	 * @param boolean $output 是否输出图像
	 * @param string $destType 缩略图文件格式{@see self::$ALLOW_FILE_TYPES}. 默认为null, 保持原来的文件类型.
	 * @param integer $quality 图片质量. 仅在缩略图格式为 jpeg 时有效, 0 ~ 100.
	 */
	public function createThumbnail($sourceImagePath, $destImagePath, $output = false, $destType = null, $quality = 100)
	{
		try
		{
			if(!file_exists($sourceImagePath))
				return false;

			$this->_sourceImage = $sourceImagePath;
		}
		catch (Exception $exception)
		{
			exit($exception->getMessage());
		}

	    try
	    {
	    	if(!$imageInfo = getimagesize($sourceImagePath))
	    		throw new Exception('Invalid image file. (' . __FILE__ . ':' . __LINE__ . ')');
	    }
	    catch (Exception $exception)
	    {
	    	exit($exception->getMessage());
	    }

	    try
	    {
	    	if($destType !== null && !in_array($destType, Thumbnail::$ALLOW_FILE_TYPES))
	    		throw new Exception('Not allowed image type. (' . __FILE__ . ':' . __LINE__ . ')');
	    }
	    catch (Exception $exception)
		{
			echo $exception->getMessage();
			$destType = null;
		}

		try
		{
			if($quality < 0 || $quality > 100)
				throw new Exception('Invalid image quality argument. (' . __FILE__ . ':' . __LINE__ . ')');
		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
			$quality = 80;
		}

	    $this->_sourceImageWidth    = intval($imageInfo[0]);
	    $this->_sourceImageHeight   = intval($imageInfo[1]);
	    $this->_sourceImageType     = image_type_to_extension(intval($imageInfo[2]), false);
	    $this->_destImageType       = $destType === null ? $this->_sourceImageType : $destType;

	    $this->_calculateImageSize();

	    $this->_destImage = imagecreatetruecolor($this->_destImageWidth, $this->_destImageHeight);
	    $function_name    = 'imagecreatefrom' . $this->_sourceImageType;
	    $sourceImage      = $function_name($sourceImagePath);

	    imagecopyresampled($this->_destImage, $sourceImage, 0, 0, 0, 0, $this->_destImageWidth, $this->_destImageHeight, $this->_sourceImageWidth, $this->_sourceImageHeight);

	    $function_name = 'image' . $this->_destImageType;
	    if($destImagePath)
	    {
	    	if($this->_destImageType == 'jpeg')
	    		$function_name($this->_destImage, $destImagePath, $quality);
	    	else
	    		$function_name($this->_destImage, $destImagePath);
	    }
	   	if($output)
	   	{
	   		header('Content-Type: image/' . $this->_destImageType);
	   		if($this->_destImageType == 'jpeg')
	    		$function_name($this->_destImage, null, $quality);
	    	else
	    		$function_name($this->_destImage);
	   	}

	   	imagedestroy($this->_destImage);
	   	imagedestroy($sourceImage);
	}

	/**
	 * 计算缩略图尺寸
	 *
	 * @access private
	 */
	private function _calculateImageSize()
	{
		$percent = 1;

		if(!is_null($this->_maxWidth) || !is_null($this->_maxHeight))
		{
			if(!is_null($this->_maxWidth) && !is_null($this->_maxHeight))
			{
				if(($this->_maxWidth / $this->_sourceImageWidth) <= ($this->_maxHeight / $this->_sourceImageHeight))
				{
					$percent = $this->_maxWidth / $this->_sourceImageWidth;
					$this->_destImageWidth  = $this->_maxWidth;
					$this->_destImageHeight = intval($this->_sourceImageHeight * $percent);
				}
				else
				{
					$percent = $this->_maxHeight / $this->_sourceImageHeight;
					$this->_destImageWidth  = intval($this->_sourceImageWidth * $percent);
					$this->_destImageHeight = $this->_maxHeight;
				}
			}
			else
			{
				if(!is_null($this->_maxWidth))
				{
					$percent = $this->_maxWidth / $this->_sourceImageWidth;
					$this->_destImageWidth  = $this->_maxWidth;
					$this->_destImageHeight = intval($this->_sourceImageHeight * $percent);
				}
				else
				{
					$percent = $this->_maxHeight / $this->_sourceImageHeight;
					$this->_destImageWidth  = intval($this->_sourceImageWidth * $percent);
					$this->_destImageHeight = $this->_maxHeight;
				}
			}
		}
		else
		{
			$percent = $this->_percent;
			$this->_destImageWidth  = intval($this->_sourceImageWidth * $percent);
			$this->_destImageHeight = intval($this->_sourceImageHeight * $percent);
		}
	}
}