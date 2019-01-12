<?php
/**
 * @class  imageprocessModel
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  imageprocess 모듈의 model 클래스
 **/

class imageprocessModel extends imageprocess 
{

	/**
	 * @brief 초기화
	 **/
	function init() 
	{
	}

	function createImageFile($source_file, $resize_width, $resize_height = 0, $ipConfig) 
	{
		$logged_info = Context::get('logged_info');
		$noresizegroup = explode(";",$ipConfig->noresizegroup);
		if(count($noresizegroup)==1)
		{
			if(array_key_exists($noresizegroup[0],$logged_info->group_list)) return;
		}
		elseif(count($noresizegroup) > 1)
		{
			foreach($noresizegroup as $group)
			{
				if(array_key_exists($group,$logged_info->group_list)) return;
			}
		}
		$quality = $ipConfig->resize_quality;
		if(!$quality) $quality = 100;
		$source_file = FileHandler::getRealPath($source_file);

		if(!file_exists($source_file)) return;
		if(!$resize_width) $resize_width = 100;
		if(!$resize_height) $resize_height = $resize_width;

		// retrieve source image's information
		$imageInfo = getimagesize($source_file);
		if(!FileHandler::checkMemoryLoadImage($imageInfo)) $this->check_memory_limit();
		list($width, $height, $type, $attrs) = $imageInfo;

		if($width<1 || $height<1) return;

		switch($type) 
		{
			case '1' :
					$type = 'gif';
				break;
			case '2' :
					$type = 'jpg';
				break;
			case '3' :
					$type = 'png';
				break;
			default :
					return;
				break;
		}

		// if original image is larger than specified size to resize, calculate the ratio
		if($resize_width > 0 && $width >= $resize_width) $width_per = $resize_width / $width;
		else $width_per = 1;

		if($resize_height > 0 && $height >= $resize_height) $height_per = $resize_height / $height;
		else $height_per = 1;

		if($width_per > $height_per) $per = $height_per;
		else $per = $width_per;
		$resize_width = $width * $per;
		$resize_height = $height * $per;

		if(!$per) $per = 1;

		// get type of target file
		$target_type = strtolower($type);

		// create temporary image with target size
		if(function_exists('imagecreatetruecolor')) $thumb = @imagecreatetruecolor($resize_width, $resize_height);
		else $thumb = @imagecreate($resize_width, $resize_height);

		$white = @imagecolorallocate($thumb, 255,255,255);
		@imagefilledrectangle($thumb,0,0,$resize_width-1,$resize_height-1,$white);

		// create temporary image having original type
		switch($type) 
		{
			case 'gif' :
				if(!function_exists('imagecreatefromgif')) return false;
				$source = @imagecreatefromgif($source_file);
				break;
			// jpg
			case 'jpeg' :
			case 'jpg' :
				if(!function_exists('imagecreatefromjpeg')) return false;
				$source = @imagecreatefromjpeg($source_file);
				break;
			// png
			case 'png' :
				if(!function_exists('imagecreatefrompng')) return false;
				$source = @imagecreatefrompng($source_file);
				break;
			default :
				return;
		}

		// resize original image and put it into temporary image
		$new_width = (int)($width * $per);
		$new_height = (int)($height * $per);
		$x = 0;
		$y = 0;

		if($source) 
		{
			if(function_exists('imagecopyresampled')) @imagecopyresampled($thumb, $source, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
			else @imagecopyresized($thumb, $source, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
		} else return false;

		// create directory
		$path = dirname($target_file);
		if(!is_dir($path)) FileHandler::makeDir($path);

		// write into the file
		switch($target_type) 
		{
			case 'gif' :
				if(!function_exists('imagegif')) return false;
				$output = @imagegif($thumb, $source_file);
				break;
			case 'jpeg' :
			case 'jpg' :
				if(!function_exists('imagejpeg')) return false;
				$output = @imagejpeg($thumb, $source_file, $quality);
				break;
			case 'png' :
				if(!function_exists('imagepng')) return false;
				$output = @imagepng($thumb, $source_file, 9);
				break;
		}

		@imagedestroy($thumb);
		@imagedestroy($source);

		if(!$output) return false;
		@chmod($target_file, 0644);

		return true;
	}

//여기부터
        function alphaWatermark($source_file,$ipConfig)
        {
	        $logged_info = Context::get('logged_info');
			$nowatergroup = explode(";",$ipConfig->nowatergroup);
			if(count($nowatergroup)==1)
			{
				if(array_key_exists($nowatergroup[0],$logged_info->group_list)) return;
			}
			elseif(count($nowatergroup) > 1)
			{
				foreach($nowatergroup as $group)
				{
					if(array_key_exists($group,$logged_info->group_list)) return;
				}
			}
			$oModuleModel = &getModel('module');
			$module_info = $oModuleModel->getModuleInfoByMid(Context::get('mid'));

			$each_watermark = $ipConfig->each_watermark;
			$each_xmargin = $ipConfig->each_xmargin;
			$each_ymargin = $ipConfig->each_ymargin;
			$each_position = $ipConfig->each_position;
			$t_mid = $module_info->module_srl;

			if(!$ipConfig->water_quality) $ipConfig->water_quality = 100;
			$source_file = FileHandler::getRealPath($source_file);

			if($each_position[$t_mid]) $position = $each_position[$t_mid];
			else $position = $ipConfig->water_position;
			if($each_watermark[$t_mid])
			{
				$water = FileHandler::getRealPath($each_watermark[$t_mid]);
			}
			else $water = FileHandler::getRealPath($ipConfig->watermark);
			if($each_xmargin[$t_mid]) $xmargin = $each_xmargin[$t_mid];
			else $xmargin = $ipConfig->xmargin;
			if($each_ymargin[$t_mid]) $ymargin = $each_ymargin[$t_mid];
			else $ymargin = $ipConfig->ymargin;

			$imageInfo = getimagesize($source_file);
			if(!FileHandler::checkMemoryLoadImage($imageInfo)) $this->check_memory_limit();

			list($width, $height, $type, $attrs) = $imageInfo;
			if($width < 1 || $height < 1) return;

			switch($type)
			{
				case '1' :
					$type = 'gif';
					break;
				case '2' :
					$type = 'jpg';
					break;
				case '3' :
					$type = 'png';
					break;
				default :
					return;
					break;
			}

			// Load the stamp and the photo to apply the watermark to
			$stamp = @imagecreatefrompng($water);

			 // create temporary image having original type
			switch($type)
			{
				case 'gif' :
					if(!function_exists('imagecreatefromgif')) return false;
					$im = @imagecreatefromgif($source_file);
					break;
				// jpg
				case 'jpeg' :
				case 'jpg' :
					if(!function_exists('imagecreatefromjpeg')) return false;
					$im = @imagecreatefromjpeg($source_file);
					break;
				// png
				case 'png' :
					if(!function_exists('imagecreatefrompng')) return false;
					$im = @imagecreatefrompng($source_file);
					break;
				default :
					return;
			}


			if(!function_exists('imagecopy')) return false;

			// Set the margins for the stamp and get the height/width of the stamp image
			$geox = 100;
			$geoy = 60;
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);
			$cx = (imagesx($im) - $sx) / 2;
			$cy = (imagesy($im) - $sy) / 2;
			if($position == "RT")
			{
					$locax = imagesx($im) - $sx - $xmargin;
					$locay = $ymargin;
			}
			elseif($position == "CE")
			{
					$locax = $cx;
					$locay = $cy;
			}
			elseif($position == "LT")
			{
					$locax = $xmargin;
					$locay = $ymargin;
			}
			elseif($position == "LB")
			{
					$locax = $xmargin;
					$locay = imagesy($im) - $sy - $ymargin;
			}
			elseif($position == "SE")
			{
					$locax = $cx + $geox;
					$locay = $cy + $geoy;
			}
			elseif($position == "NE")
			{
					$locax = $cx + $geox;
					$locay = $cy - $geoy;
			}
			elseif($position == "NW")
			{
					$locax = $cx - $geox;
					$locay = $cy - $geoy;
			}
			elseif($position == "SW")
			{
					$locax = $cx - $geox;
					$locay = $cy + $geoy;
			}
			else
			{
					$locax = imagesx($im) - $sx - $xmargin;
					$locay = imagesy($im) - $sy - $ymargin;
			}

			// Copy the stamp image onto our photo using the margin offsets and the photo
			// width to calculate positioning of the stamp.
			imagecopy($im, $stamp, $locax, $locay, 0, 0, $sx, $sy);

			// write into the file
			switch($type)
			{
					case 'gif' :
									if(!function_exists('imagegif')) return false;
									$output = @imagegif($im, $source_file);
							break;
					case 'jpeg' :
					case 'jpg' :
									if(!function_exists('imagejpeg')) return false;
									$output = @imagejpeg($im, $source_file, $ipConfig->water_quality);
							break;
					case 'png' :
									if(!function_exists('imagepng')) return false;
									$output = @imagepng($im, $source_file, 9);
							break;
			}

			@imagedestroy($im);
			@imagedestroy($stamp);

			return true;
        }

//요까지

	function GDrotate($file,$ext) 
	{
		$exif = exif_read_data($file);
		if(!empty($exif['Orientation'])) 
		{
			$imageInfo = @getimagesize($file);
			if(!FileHandler::checkMemoryLoadImage($imageInfo)) $this->check_memory_limit();

			if($ext == "jpg" || $ext == "jpeg")
			{
				$image = imagecreatefromjpeg($file);
			}
			else if($ext == "png")
			{
				$image = imagecreatefrompng($file);
			}
			else if($ext == "bmp" || $ext == "wbmp")
			{
				$image = imagecreatefromwbmp($file);
			}
			else if($ext == "gif")
			{
				$image = imagecreatefromgif($file);
			}

			switch($exif['Orientation']) 
			{
				case 8:
					$image = imagerotate($image,90,0);
					break;
				case 3:
					$image = imagerotate($image,180,0);
					break;
				case 6:
					$image = imagerotate($image,-90,0);
					break;
			}
			if($ext == "jpg" || $ext == "jpeg")
			{
				imagejpeg($image,$file);
			} 
			else if($ext == "png")
			{
				imagepng($image,$file);
			}
			else if($ext == "bmp" || $ext == "wbmp")
			{
				imagewbmp($image,$file);
			}
			else if($ext == "gif")
			{
				imagegif($image,$file);
			}
			@imagedestroy($image);
		}
	}

	function MagicRotate($file,$magic_path) 
	{
		$realfile = FileHandler::getRealpath($file);

        $exif = exif_read_data($realfile);
		$fn = substr(strrchr($file,'/'),1);
        $out = '1_'.$fn;
        $work_path = dirname($realfile);
        $outfile = $work_path.'/'.$out;
        $ext = strtolower(substr(strrchr($file,'.'),1));
        $magic_path = str_replace('\\','/',$magic_path);
        $command = $magic_path."convert";

        if(!empty($exif['Orientation'])) 
		{
			$args[] = "-auto-orient";
//			$args[] = " -strip";
			$args[] = $fn;
            $args[] = $out;
			$out = $this->_imagemagick_convert_exec($args, $work_path, $command);
        	$this->moveFile($outfile,$realfile);
	        return;
       }
    }

	function magicResize($file,$new_width,$new_height,$ipConfig) 
	{
		$logged_info = Context::get('logged_info');
		$noresizegroup = explode(";",$ipConfig->noresizegroup);
		if(count($noresizegroup)==1)
		{
			if(array_key_exists($noresizegroup[0],$logged_info->group_list)) return;
		}
		elseif(count($noresizegroup) > 1)
		{
			foreach($noresizegroup as $group)
			{
				if(array_key_exists($group,$logged_info->group_list)) return;
			}
		}
		$quality = $ipConfig->resize_quality;
		if(!$quality) $quality = 100;
		$magic_path = $ipConfig->magic_path;
		$realfile = FileHandler::getRealpath($file);
		list($width, $height,$type)=getimagesize($realfile);
        if($width <=$new_width && $height <=$new_height)
		{
			//$this->moveFile($outfile,$realfile);
			return;
		}
		$fn = substr(strrchr($file,'/'),1);
		$out = '1_'.$fn;
		$work_path = dirname($realfile);
		$outfile = $work_path.'/'.$out;
		$ext = strtolower(substr(strrchr($file,'.'),1));
		$magic_path = str_replace('\\','/',$magic_path);
		$command = $magic_path."convert";
	
		if($ext == 'gif') //for animated GIF
        {

            $args[] = $fn;
            $args[] = "-coalesce";
            $args[] = "-resize " . $new_width . 'x' . $new_height ;
            $args[] = $out;
            $this->_imagemagick_convert_exec($args, $work_path, $command);
        }
		else 
		{
			$args[] = "-compress JPEG"; 
			$args[] = "-quality ".$quality ;
			$args[] = "-resize " . $new_width . 'x' . $new_height ;
			$args[] = $fn;
			$args[] = $out;

			$out = $this->_imagemagick_convert_exec($args, $work_path, $command);
		}
		$this->moveFile($outfile,$realfile);

		return;
	}

	function magicConvert($file,$magic_path,$target_format,$or_format=null,$tarsize=null)
	{
		$raw_format = array('arw','raf','orf','crw','cr2','dng','pef','mrw','x3f','nef'); //for RAW format support
		//$flat_format = array('psd','eps','xcf','gif');
		$realfile = FileHandler::getRealpath($file);
		$fn = substr(strrchr($file,'/'),1);
		$out = $fn.".".$target_format;
		$work_path = dirname($realfile);
		$outfile = $work_path.'/'.$out;
		$command = $magic_path.'convert';
		$args[] = $or_format.':'.$fn;	
		if(in_array($or_format,$raw_format))
		{
			$args[] = '-size '.$tarsize.'x';
			$args[] = '-depth 8';
		}
		elseif($or_format == 'eps') 
		{
			unset($args);
			$args[] = '-verbose';
			$args[] = '-density 600';
			$args[] = '-geometry 50%';
			$args[] = $or_format.':'.$fn;	
		}
		elseif( $or_format == 'psd')
		{
			$args[] = '-flatten';
		} 
		$args[] = $target_format.':'.$out;
		$this->_imagemagick_convert_exec($args, $work_path, $command);

		if(file_exists($outfile)) return $outfile;
		else return;
	}

	function magicWatermark($file,$ipConfig)
	{
		$logged_info = Context::get('logged_info');
		$nowatergroup = explode(";",$ipConfig->nowatergroup);
		if(count($nowatergroup)==1)
		{
			if(array_key_exists($nowatergroup[0],$logged_info->group_list)) return;
		}
		elseif(count($nowatergroup) > 1)
		{
			foreach($nowatergroup as $group)
			{
				if(array_key_exists($group,$logged_info->group_list)) return;
			}
		}

		if(!$ipConfig->water_quality) $ipConfig->water_quality = 100;
		$realfile = realpath($file);
		$fn = substr(strrchr($file,'/'),1);
		$out = '1_'.$fn;
		$work_path = dirname($realfile);
		$ext = strtolower(substr(strrchr($file,'.'),1));
		$outfile = $work_path.'/'.$out;

// 추가 //
		$oModuleModel = &getModel('module');
		$module_info = $oModuleModel->getModuleInfoByMid(Context::get('mid'));

		$each_watermark = $ipConfig->each_watermark;
        $each_xmargin = $ipConfig->each_xmargin;
        $each_ymargin = $ipConfig->each_ymargin;
        $each_position = $ipConfig->each_position;
        $t_mid = $module_info->module_srl;
//

		if($each_position[$t_mid]) $water_position = $each_position[$t_mid];
        else $water_position = $ipConfig->water_position;

		if($each_watermark[$t_mid]) {
            $water = FileHandler::getRealPath($each_watermark[$t_mid]);
        }
		else $water = FileHandler::getRealPath($ipConfig->watermark);

        if($each_xmargin[$t_mid]) $xmargin = $each_xmargin[$t_mid];
        else $xmargin = $ipConfig->xmargin;

        if($each_ymargin[$t_mid]) $ymargin = $each_ymargin[$t_mid];
        else $ymargin = $ipConfig->ymargin;
// 추가끝 //
		$command= $ipConfig->magic_path.'composite'; 
		$args[] = '-quality '.$ipConfig->water_quality;
		$args[] = '-gravity '.$this->getGeo($water_position, $xmargin, $ymargin );
		$args[] = realpath($water);
		$args[] = $fn;
		$args[] = $out;
		$out = $this->_imagemagick_convert_exec($args, $work_path, $command);
		$this->moveFile($outfile,$realfile);
		return;
	}


	function check_memory_limit() 
	{
		if (PHP_INT_MAX == '2147483647' && (substr(ini_get('memory_limit'), 0, -1) < '128')) 
		{
			// 32bit PHP
			@ini_set('memory_limit', '120M');
			return true;
		}
		else if (PHP_INT_MAX == '9223372036854775807' && (substr(ini_get('memory_limit'), 0, -1) < '512')) 
		{
			// 64bit PHP
			@ini_set('memory_limit', '512M');
			return true;
		}
		return false;
	}

	/**
	 * @brief DB에 생성된 mid 전체 목록을 구해옴
	 **/
	function getMidList($args = null) 
	{
		$output = executeQuery('imageprocess.getMidList', $args);
		if(!$output->toBool()) return $output;

		$list = $output->data;
		if(!$list) return;

		if(!is_array($list)) $list = array($list);

		foreach($list as $val) 
		{
			$mid_list[$val->module_srl] = $val;
		}
		return $mid_list;
	}
	
	function moveFile($out,$real) 
	{
		if(!file_exists($out)) sleep(1);
		if(!file_exists($out)) return;
		FileHandler::moveFile($out,$real);
	}
	


	function _imagemagick_convert_exec($command_args, $work_path, $convert_path) 
	{
		if (!isset($convert_path)) 
		{
			return FALSE;
		}

		if (stripos(PHP_OS, 'WIN') === 0) 
		{
			$convert_path = 'start "ImageMagick" /D ' . escapeshellarg($work_path) . ' /B ' . escapeshellarg($convert_path);
		}
		$command = $convert_path . ' ' . implode(" ",$command_args);
			//$this->Debug($command);	
		$descriptors = array(
			// stdin
			0 => array('pipe', 'r'),
			// stdout
			1 => array('pipe', 'w'),
			// stderr
			2 => array('pipe', 'w'),
		);
		if ($h = proc_open($command, $descriptors, $pipes, $work_path)) 
		{
			$output = '';
			while (!feof($pipes[1])) 
			{
				$output .= fgets($pipes[1]);
			}
			$error = '';
			while (!feof($pipes[2])) 
			{
				$error .= fgets($pipes[2]);
			}
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			return proc_close($h);
		}
		return FALSE;
	}

	
	function getGeo($position,$xmargin=10,$ymargin=10)
	{
		$margin = '+'.$xmargin.'+'.$ymargin;
		if($position == 'NE') $args[] = 'center  -geometry +150-100';
		elseif($position == 'SW') $geo = 'center  -geometry -150+100';
		elseif($position == 'SE') $geo = 'center  -geometry +150+100';
		elseif($position == 'CE') $geo = 'center';
		elseif($position == 'NW') $geo = 'center  -geometry -150-100';
		elseif($position == 'RT') $geo = 'NorthEast -geometry '.$margin;
		elseif($position == 'LT') $geo = 'NorthWest -geometry '.$margin;
		elseif($position == 'LB') $geo = 'SouthWest -geometry '.$margin;
		else $geo = 'SouthEast -geometry '.$margin;
		return $geo;
	}

	/*
	* 포맷변경을 한경우 원본화일을 체크
	* 소스화일의 확장자가 변경되고 확장자 없는 화일이 있는지 체크
	*/
	function checkConvertedFile($args)
	{
		$ext1 = strtolower(substr(strrchr($args->source_filename,'.'),1));
		$ext2 = strtolower(substr(strrchr($args->uploaded_filename,'.'),1));
		$real_file = str_replace('.'.$ext2,'',$args->uploaded_filename);
		if($ext1 != $ext2 && file_exists($real_file)) return $real_file;
		else return false;
	}
	
	// 원본화일의 다운로드 권한설정
	function getGrantDown($args) 
	{
		$logged_info=Context::get('logged_info');
		if(!$logged_info) return false;
		if($logged_info->is_admin == 'Y') return true;
		if($logged_info->member_srl == $args->member_srl) return true;
		foreach($args->down_group as $group) if(array_key_exists($group,$logged_info->group_list)) return true;
		return false;
	}

	//원본화일 체크
	function getOfile($file,$t_path='') 
	{
		$ofile = NULL;
		$s_file = substr(strrchr($file,'/'),1);
		$s_name = 'XeOrg_'.$s_file;
		if($t_path) 
		{
			$p_list=explode('/',$file);
            for($i=3;$i<count($p_list)-1;$i++) 
			{
                $t_path .= '/'.$p_list[$i];
                if(!is_dir($t_path)) @mkdir($t_path,0755);
            }
			$ofile = $t_path.'/'.$s_file;
		} else $ofile = str_replace($s_file,$s_name,$file);
		return $ofile;
	}
	
	function checkOfile($file,$t_path) 
	{
		$ofile = NULL;
		$s_file = substr(strrchr($file,'/'),1);
		$s_name = 'XeOrg_'.$s_file;
        if($t_path) 
		{
            $p_list=explode('/',$file);
            for($i = 3; $i < count($p_list) - 1; $i++) $t_path .= '/'.$p_list[$i];
            $ofile = $t_path.'/'.$s_file;
        } 
		else $ofile = str_replace($s_file,$s_name,$file);
        return $ofile;
    }

	function getFolder($file,$depth=0) 
	{
		if(!$depth) $d =2;
		else $d = 3;
		$p_list=explode('/',$file);
		$path=$p_list[0];
		for($i=1;$i<count($p_list) - $d;$i++) $path .= '/'.$p_list[$i];
		return $path;
	}

	function deleteOFiles($upload_target_srl,$store_path) 
	{
		// 첨부파일 목록을 받음
		$oFileModel = &getModel('file');
		$file_list = $oFileModel->getFiles($upload_target_srl);

		// 첨부파일이 없으면 성공 return
		if(!is_array($file_list)||!count($file_list)) return new Object();

		// 실제 파일 삭제
		$path = array();
		$file_count = count($file_list);
		for($i=0;$i<$file_count;$i++) 
		{
			$s_file = $file_list[$i]->source_filename;
			$file = $file_list[$i]->uploaded_filename;
			$ofile = $this->checkOfile($file,$store_path);
			FileHandler::removeFile($ofile);
			$path = $this->getFolder($ofile);
			FileHandler::removeBlankDir($path);
		}

		return $output;
	}

	function getConversionName($args) 
	{
		$path = sprintf('./files/attach/images/%s/%s', $args->module_srl,getNumberingPath($args->upload_target_srl,3));
		$file_name = substr(strrchr($args->uploaded_filename,'/'),1);
		$file = sprintf('%s%s',$path,$file_name);
		FileHandler::makeDir($path);
		copy($args->uploaded_filename,$file);
		if(!file_exists($file)) sleep(1);
		if(file_exists($file)) 
		{
			FileHandler::removeFile($args->uploaded_filename);
			return $file;
		} 
		else return false;
	}

	function alphaTextLogo($source_file,$config)
    {
        $font = $config->exfont;
        $position = $config->logo_position;
        $point = $config->logo_point;
        $quality = $config->logo_quality;
//      $color = $config->logo_fg;
        if(!$quality) $quality = 100;

        $source_file = FileHandler::getRealPath($source_file);
        $logged_info = Context::get('logged_info');
		$config->nologogroup=explode(";",$config->nologogroup);
		foreach($config->nologogroup as $group) if(array_key_exists($group,$logged_info->group_list)) return;
        $logged_info->time = date('Y-m-d',time());
        if(!$logged_info) {
            $logged_info->user_id = '';
            $logged_info->nick_name = '';
            $logged_info->email_address ='';
            $logged_info->user_name = '';
        }
        $textlogo = $this->mergeKeywords($config->textlogo,$logged_info);
        $imageInfo = getimagesize($source_file);
        list($width, $height, $type, $attrs) = $imageInfo;
        if($width < 1 || $height < 1) return;

        switch($type)
        {
            case '1' :
                    $type = 'gif';
                break;
            case '2' :
                    $type = 'jpg';
                break;
            case '3' :
                    $type = 'png';
                break;
            default :
                    return;
                break;
        }

        // create temporary image having original type
        switch($type)
        {
            case 'gif' :
				if(!function_exists('imagecreatefromgif')) return false;
				$im = @imagecreatefromgif($source_file);
                break;
            // jpg
            case 'jpeg' :
            case 'jpg' :
				if(!function_exists('imagecreatefromjpeg')) return false;
				$im = @imagecreatefromjpeg($source_file);
                break;
            // png
            case 'png' :
				if(!function_exists('imagecreatefrompng')) return false;
				$im = @imagecreatefrompng($source_file);
                break;
            default :
                return;
        }

        // Set the margins for the stamp and get the height/width of the stamp image
        $marge = 10;
        $geox = 100;
        $geoy = 60;
        $tbox = $this->calculateTextBox($textlogo,$font,$point,0);
        $sx = $tbox["width"];
        $sy = $tbox["height"];

        if($position == "center")
        {
            $locax = ($width - $sx) / 2;
            $locay = ($height + $sy) / 2;
        }
        elseif($position == "south")
        {
            $locax = ($width - $sx) / 2;
            $locay = $height - $marge;
        }
        elseif($position == "north")
        {
            $locax = ($width - $sx) / 2;
            $locay = $marge + $sy;
        }
        elseif($position == "northwest")
        {
            $locax = $marge;
            $locay = $marge + $sy;
        }
        elseif($position == "southwest")
        {
            $locax = $marge;
            $locay = $height - $marge;
        }
        elseif($position == "southeast")
        {
            $locax = $width - $sx - $marge;
            $locay = $height - $marge;
        }
        else //if($position == "northeast")
        {
            $locax = $width - $sx - $marge;
            $locay = $marge + $sy;
        }

        $bgrgb = $this->rgb2array($config->logo_bg);
        $bg = imagecolorallocate($im, $bgrgb[0], $bgrgb[1], $bgrgb[2]);
        if($config->logo_style =='stroke')
        {
            imagefttext($im, $point, 0, $locax+1, $locay+1, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax+1, $locay, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax-1, $locay, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax-1, $locay-1, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax+1, $locay-1, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax, $locay-1, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax-1, $locay+1, $bg, $font, $textlogo);
            imagefttext($im, $point, 0, $locax, $locay+1, $bg, $font, $textlogo);
        }
        elseif($config->logo_style =='shadow') imagefttext($im, $point, 0, $locax-1, $locay-1, $bg, $font, $textlogo);
        $fgrgb = $this->rgb2array($config->logo_fg);

        $fg =  imagecolorallocate($im, $fgrgb[0], $fgrgb[1], $fgrgb[2]);
        imagefttext($im, $point, 0, $locax, $locay, $fg, $font, $textlogo);

    // write into the file
        switch($type)
        {
            case 'gif' :
				if(!function_exists('imagegif')) return false;
				$output = @imagegif($im, $source_file);
                break;
            case 'jpeg' :
            case 'jpg' :
				if(!function_exists('imagejpeg')) return false;
				$output = @imagejpeg($im, $source_file, $quality);
                break;
            case 'png' :
				if(!function_exists('imagepng')) return false;
				$output = @imagepng($im, $source_file, 9);
                break;
        }
        @imagedestroy($im);
        if(!$output) return false;
        return true;
    }

    function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) 
	{
        $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text);
        $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
        $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));

        return array(
         "left"   => abs($minX) - 1,
         "top"    => abs($minY) - 1,
         "width"  => $maxX - $minX,
         "height" => $maxY - $minY,
         "box"    => $rect
        );
    }

    function magicTextLogo($file,$config)
    {
		$logged_info = Context::get('logged_info');
        $config->nologogroup=explode(";",$config->nologogroup);
        foreach($config->nologogroup as $group) if(array_key_exists($group,$logged_info->group_list)) return;

        if(!$config->quality) $config->quality = 100;
        $realfile = realpath($file);
        $fn = substr(strrchr($file,'/'),1);
        $out = '1_'.$fn;
        $work_path = dirname($realfile);
        $ext = strtolower(substr(strrchr($file,'.'),1));
        $outfile = $work_path.'/'.$out;
        if (!stristr(PHP_OS, 'WIN'))
        {
            $fn = $realfile;
            $out = $outfile;
        }
        $font = $config->exfont;

        $logged_info->time = date('Y-m-d',time());
        if(!$logged_info) {
            $logged_info->user_id = '';
            $logged_info->nick_name = '';
            $logged_info->email_address ='';
            $logged_info->user_name = '';
        }
        $textlogo = $this->mergeKeywords($config->textlogo,$logged_info);
        if (stristr(PHP_OS, 'WIN')) $textlogo = iconv("UTF-8","EUC-KR",$textlogo);
        if($config->logo_style =='stroke') $draw_command = sprintf( "gravity %s fill '%s' text 12,2 '%s' fill '%s' text 12,4 '%s' fill '%s' text 14,3 '%s' fill '%s' text 13,2 '%s' fill '%s' text 12,3 '%s' fill '%s' text 13,4 '%s' fill '%s' text 14,4 '%s' fill '%s' text 14,2 '%s' fill '%s' text 13,3 '%s'", $config->logo_position, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_bg, $textlogo, $config->logo_fg, $textlogo );
        elseif($config->logo_style =='simple') $draw_command = sprintf("gravity %s fill '%s' text 14,4 '%s'",$config->logo_position, $config->logo_fg, $textlogo);
        else $draw_command = sprintf("gravity %s fill '%s' text 13,3 '%s' fill '%s' text 14,4 '%s'",$config->logo_position, $config->logo_bg,$textlogo,$config->logo_fg, $textlogo);
        $command= $config->magic_path.'convert';
        $args[] = $fn;
        $args[] = '-quality '.$config->logo_quality;
        $args[] = '-font '.$font;
        $args[] = '-pointsize '.$config->logo_point;
        $args[] = '-draw "'.$draw_command.'"';
        $args[] = $out;

        $out = $this->_imagemagick_convert_exec($args, $work_path, $command);
        $this->moveFile($outfile,$realfile);
        return;
    }

	function mergeKeywords($text, &$obj) {
        if (!is_object($obj)) return $text;

        foreach ($obj as $key => $val)
        {
            if (is_array($val)) $val = join($val);
            if (is_string($key) && is_string($val)) {
                if (substr($key,0,10)=='extra_vars') $val = str_replace('|@|', '-', $val);
                $text = preg_replace("/%" . preg_quote($key) . "%/", $val, $text);
            }
        }
        return $text;
    }

    function rgb2array($rgb) {
        $rgb = str_replace('#','',$rgb);
        return array(
            base_convert(substr($rgb, 0, 2), 16, 10),
            base_convert(substr($rgb, 2, 2), 16, 10),
            base_convert(substr($rgb, 4, 2), 16, 10),
        );
    }


}
/* End of file imageprocess.model.php */
/* Location: ./modules/imageprocess/imageprocess.model.php */
