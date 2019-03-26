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

	function GDResize($source_file, $resize_width, $resize_height = 0, $ipConfig) 
	{
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

        function GDWatermark($source_file,$ipConfig)
        {
		$oModuleModel = &getModel('module');
		$module_info = $oModuleModel->getModuleInfoByMid(Context::get('mid'));

		$each_watermark = $ipConfig->each_watermark;
		$each_xmargin = $ipConfig->each_xmargin;
		$each_ymargin = $ipConfig->each_ymargin;
		$each_position = $ipConfig->each_water_position;
		$t_mid = $module_info->module_srl;

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

		$stamp = @imagecreatefrompng($water);

		switch($type)
		{
			case 'gif' :
				if(!function_exists('imagecreatefromgif')) return false;
				$im = @imagecreatefromgif($source_file);
				break;
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

		$geox = imagesx($im)/4;
		$geoy = imagesy($im)/4;
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

		imagecopy($im, $stamp, $locax, $locay, 0, 0, $sx, $sy);

		switch($type)
		{
			case 'gif' :
				if(!function_exists('imagegif')) return false;
				$output = @imagegif($im, $source_file);
				break;
			case 'jpeg' :
			case 'jpg' :
				if(!function_exists('imagejpeg')) return false;
				$output = @imagejpeg($im, $source_file, 100);
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

	function GDrotate($file) 
	{
		$ext = strtolower(substr(strrchr($file,'.'),1));
		$exif = @exif_read_data($file);
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

	function magicRotate($file,$magic_path) 
	{
		$realfile = FileHandler::getRealpath($file);

	        $exif = @exif_read_data($realfile);
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
			$args[] = $fn;
            		$args[] = $out;
			$out = $this->_imagemagick_convert_exec($args, $work_path, $command);
        	$this->moveFile($outfile,$realfile);
	        return;
       		}
    	}

	function magicResize($file,$new_width,$new_height,$ipConfig) 
	{
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
			$args[] = "-quality ".$ipConfig->resize_quality ;
			$args[] = "-resize " . $new_width . 'x' . $new_height ;
			$args[] = $fn;
			$args[] = $out;

			$out = $this->_imagemagick_convert_exec($args, $work_path, $command);
		}
		$this->moveFile($outfile,$realfile);
		return;
	}

	function magicWatermark($file,$ipConfig)
	{
		$realfile = realpath($file);
		$fn = substr(strrchr($file,'/'),1);
		$out = '1_'.$fn;
		$work_path = dirname($realfile);
		$ext = strtolower(substr(strrchr($file,'.'),1));
		$outfile = $work_path.'/'.$out;
		$water = FileHandler::getRealPath($ipConfig->watermark);

		$command= $ipConfig->magic_path.'composite'; 
		$args[] = '-quality 100 ';
		$args[] = '-gravity '.$this->getGeo($ipConfig);
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

	
	function getGeo($ipConfig)
	{
		$position = $ipConfig->water_position;
		$xmargin = $ipConfig->xmargin;
		$ymargin = $ipConfig->ymargin;
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
		if(!is_array($file_list)||!count($file_list)) return class_exists('BaseObject') ? new BaseObject() : new Object();

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

	function GDTextLogo($source_file,$config)
    	{
       	 	$font = $config->exfont;
	        $position = $config->logo_position;
        	$point = $config->logo_point;

        	$source_file = FileHandler::getRealPath($source_file);
	        $logged_info = Context::get('logged_info');
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
            		case 'jpeg' :
            		case 'jpg' :
				if(!function_exists('imagecreatefromjpeg')) return false;
				$im = @imagecreatefromjpeg($source_file);
                	break;
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
				$output = @imagejpeg($im, $source_file, 100);
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
        $args[] = '-quality 100';
        $args[] = '-font '.$font;
        $args[] = '-pointsize '.$config->logo_point;
        $args[] = '-draw "'.$draw_command.'"';
        $args[] = $out;

        $out = $this->_imagemagick_convert_exec($args, $work_path, $command);
        $this->moveFile($outfile,$realfile);
        return;
    }

    function mergeKeywords($text, &$obj) 
    {
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

	function getImageprocessConfig($module_srl)
	{
		$oModuleModel = &getModel('module');
    		$imageprocess_info = $oModuleModel->getModuleConfig('imageprocess');
		$info = new stdClass;
		if($imageprocess_info->resize_use == 'Y')
		{
			$target_mid=explode(";",$imageprocess_info->target_mid);
			$info->resize = in_array($module_srl, $target_mid) ? true : false ;
		}
		if($imageprocess_info->watermark_use == 'Y')
		{
			$water_mid=explode(";",$imageprocess_info->water_mid);
	                $info->watermark = in_array($module_srl, $water_mid) ? true : false ;
			$info->each_watermark = $imageprocess_info->each_watermark[$module_srl];
			if(!$info->each_watermark) $info->each_watermark = $imageprocess_info->watermark;
			$info->each_xmargin = $imageprocess_info->each_xmargin[$module_srl];
			if(!$info->each_xmargin) $info->each_xmargin = $imageprocess_info->xmargin;
			$info->each_ymargin = $imageprocess_info->each_ymargin[$module_srl];
			if(!$info->each_ymargin) $info->each_ymargin = $imageprocess_info->ymargin;
			$info->water_position = $imageprocess_info->each_water_position[$module_srl];
			if(!$info->water_position) $info->water_position = $imageprocess_info->water_position;
		}
		if($imageprocess_info->original_store == 'Y')
		{
			$store_mid=explode(";",$imageprocess_info->store_mid);
			$info->ofile = in_array($module_srl, $store_mid) ? true : false ;
		}
		if($imageprocess_info->textlogo_use == 'Y')
		{
			$logo_mid=explode(";",$imageprocess_info->logo_mid);
			$info->textlogo = in_array($module_srl, $logo_mid) ? true : false ;
                	$logo = unserialize($imageprocess_info->each_logo);
			$info->logo = $logo[$module_srl];
			if(!$info->logo) $info->logo = $imageprocess_info->textlogo;
			$fg = unserialize($imageprocess_info->each_fg);
			$info->fg = $fg[$module_srl];
			if(!$info->fg) $info->fg = $imageprocess_info->logo_fg;
                	$bg = unserialize($imageprocess_info->each_bg);
			$info->bg = $bg[$module_srl];
			if(!$info->bg) $info->bg = $imageprocess_info->logo_bg;
                	$position = unserialize($imageprocess_info->each_text_position);
			$info->position = $position[$module_srl];
			if(!$info->position) $info->position = $imageprocess_info->logo_position;
		}
		return $info;
	}		

	function getStampList()
    	{
        	$txt = fileHandler::readDir('./modules/imageprocess/stamp');
	        $arr=array();
        	foreach ($txt as $key)
        	{
			if(strtolower(substr(strrchr($key,'.'),1)) != 'png') continue; //png 화일이 아니면 패쓰...
           	 	$dir = './modules/imageprocess/stamp/'.$key;
		            $arr[$key] = $dir;
	       	 }
        	ksort($arr);
	        return $arr;
    	}

	function insertEXIF($args)
	{
		$output = executeQuery('imageprocess.insertexif', $args);
		return $output;
	}

	function getEXIF($args)
	{
		if(!$args->file_srl) return;
//		if(!$args->target_srl) return;
		$output = executeQuery('imageprocess.getexif', $args);
		return $output->data;
	}

	function deleteEXIF($args)
        {
                $output = executeQuery('imageprocess.deleteexif', $args);
		return $output;
        }

	function imagickdo($file,$ipConfig)
	{
		$oModuleModel = &getModel('module');
                $module_info=$oModuleModel->getModuleInfoByModuleSrl($ipConfig->fileargs->module_srl);
                $file_mid= $module_info->module_srl;
		$logged_info = Context::get('logged_info');
		$ext = strtolower(substr(strrchr($file,'.'),1));

		list($original_width, $original_height, $orginal_type) = getimagesize($file);
		$image = new \Imagick($file);
                $image->setResourceLimit(imagick::RESOURCETYPE_MEMORY, 126*1024*1024);
                $image->setResourceLimit(imagick::RESOURCETYPE_MAP, 126*1024*1024);
		$exif = @exif_read_data($file, 'IFD0');
		if($ipConfig->exif_del == 'S') 
		{
			$args = new stdClass;
			$args->member_srl = $logged_info->member_srl;
			$args->file_srl = $ipConfig->fileargs->file_srl;
			$args->regdate = date('YmdHis');
			$args->target_srl = $ipConfig->fileargs->upload_target_srl;
			$args->exif = serialize($this->returnExif($exif));
			$args->gps = serialize($this->returnGps($exif));
			$output = $this->insertEXIF($args);	
		}
		if($ipConfig->exif_del == 'S' || $ipConfig->exif_del == 'Y')
		{
                	$profiles = $image->getImageProfiles("icc", true);
                        $image->stripImage();
                        if(!empty($profiles))   $image->profileImage("icc", $profiles['icc']);
		}

		$count = 0;
		$newSize = $this->getNewsize($file, $ipConfig);
		$newwidth = $newSize->width;
		$newheight = $newSize->height;

		if($ipConfig->rotate_use == 'Y' && preg_match('/\.(jpg|jpeg|gif|png)$/i', $file) )
                {
                        if($exif['Orientation'] == '6' || $exif['Orientation'] == '3' || $exif['Orientation'] == '8')
                        {
                                $count++;
				$image = $this->autorotate($image);
				if($exif['Orientation'] == '6' || $exif['Orientation'] == '8')
				{
					$newwidth = $newSize->height;
		                	$newheight = $newSize->width;
				}
                        }
                }
                if($ipConfig->resize_use == 'Y' && preg_match($ipConfig->resize_type, $file) &&  in_array($file_mid,$ipConfig->target_mid) && ($original_width > $ipConfig->resize_width || $original_height > $ipConfig->resize_width  ))
                {
                        if($this->checkGroup($ipConfig->noresizegroup))
                        {
                                if($newSize) 
				{
					$count++;
					$image->setCompression(Imagick::COMPRESSION_JPEG);
					$image->setCompressionQuality($ipConfig->resize_quality);
					$image->resizeImage($newwidth, $newheight, Imagick::FILTER_LANCZOS,1);
				}
                        }
                }

                //여기부터 워터마크
                if($ipConfig->watermark_use == 'Y' && preg_match($ipConfig->ext_type, $file) &&  in_array($file_mid,$ipConfig->water_mid) && $original_width > $ipConfig->minimum_width && $original_height > $ipConfig->minimum_width )
                {
                        if($this->checkGroup($ipConfig->nowatergroup))
                        {
				$count++;
                                $position = $ipConfig->water_position;
		                $xmargin = $ipConfig->xmargin;
                		$ymargin = $ipConfig->ymargin;
                	$watermark = new \Imagick();
	                $watermark->readImage($ipConfig->watermark);

        	        $sx = $watermark->getImageWidth();
                	$sy = $watermark->getImageHeight();
	                $imagex	= $image->getImageWidth();
        	        $imagey = $image->getImageHeight();
                	$geox = $imagex/4;
	                $geoy = $imagey/4;
        	        $cx = ($imagex - $sx) / 2;
                	$cy = ($imagey - $sy) / 2;
 			if($position == "LT")
                	{
                        	$locax = $xmargin;
	                        $locay = $ymargin;
        	        }
                	elseif($position == "CE")
                	{
                        	$locax = $cx;
                        $locay = $cy;
	                }
	                elseif($position == "RB")
        	        {
                	         $locax = $imagex - $sx - $xmargin;
                        	 $locay = $imagey - $sy - $ymargin;
                }
                elseif($position == "LB")
                {
                         $locax = $xmargin;
                         $locay = $imagey - $sy - $ymargin;
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
                        $locax = $imagex - $sx - $xmargin;
                        $locay = $ymargin;
                 }

                $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $locax, $locay);
			$watermark->clear();
                        }
                }

                //여기부터 텍스트로고
                if($ipConfig->textlogo_use == 'Y' && preg_match($ipConfig->logo_ext_type, $file) &&  in_array($file_mid,$ipConfig->logo_mid) && $original_width > $ipConfig->logo_minimum_width && $original_height > $ipConfig->logo_minimum_width )
                {
                       	if($this->checkGroup($ipConfig->nologogroup))
	                {
				$count++;
        	                $position_type = array(
                        	'southeast' => \Imagick::GRAVITY_SOUTHEAST,
                       		'south' => \Imagick::GRAVITY_SOUTH,
	                	'northeast' => \Imagick::GRAVITY_NORTHEAST,
        	        	'northwest' => \Imagick::GRAVITY_NORTHWEST,
                       		'north' => \Imagick::GRAVITY_NORTH,
                        	'southwest' => \Imagick::GRAVITY_SOUTHWEST,
	                        'center' => \Imagick::GRAVITY_CENTER,
        	        	);
//	        	        $logged_info = Context::get('logged_info');
        	        	$logged_info->time = date('Y-m-d',time());
	        	        if(!$logged_info) 
				{
        	                	$logged_info->user_id = '';
	                	        $logged_info->nick_name = '';
        	                	$logged_info->email_address ='';
	        	                $logged_info->user_name = '';
        	        	}
		                $textlogo = $this->mergeKeywords($ipConfig->textlogo,$logged_info);
        		        $gr= $position_type[$ipConfig->logo_position];
	        	        $draw = new \ImagickDraw();

                		$draw->setFont($ipConfig->exfont);
	        	        $draw->setFontSize($ipConfig->logo_point);
		                $draw->setGravity($gr);
                		if($ipConfig->logo_style == 'stroke')
        	        	{
	                        	$draw->setStrokeWidth(1);
	                        	$draw->setStrokeAntialias(true);
        	        	        $draw->setTextAntialias(true);
        		                $draw->setStrokeColor($ipConfig->logo_bg);
	                	}
	                	elseif($ipConfig->logo_style == 'shadow')
 				{
	        	                $draw->setFillColor($ipConfig->logo_bg); //set shadow color
                        		$image->annotateImage($draw, 10 + 1, 10 + 1, 0, $textlogo);
                		}
	        	        $draw->setFillColor($ipConfig->logo_fg);
		                $image->annotateImage($draw, 10, 10, 0, $textlogo);
				$draw->clear();
                	        }
                	}
			if($count) $image->writeimage($file);
                	$image->clear();
	}

	function getNewsize($file, $ipConfig)
        {
                list($width, $height,$type)=getimagesize($file);
                if(!$type || $type>3) return; //1:GIF, 2:JPG, 3:PNG
                $target_size = $ipConfig->resize_width;
                if($height <= $ipConfig->minimum_width || $width <= $ipConfig->minimum_width) return false;
                if($height <= $target_size && $width <= $target_size) return false;
                $obj = new stdClass;
                if($ipConfig->target_width == 'N' && $width>$target_size)
                {
                        $obj->width = $target_size;
                        $obj->height = round($height*$target_size/$width);
                }
                elseif ($ipConfig->target_width == 'Y' && ($width>$target_size || $height>$target_size))
                {
                        if($width>$height)
                        {
                                $obj->width = $target_size;
                                $obj->height = round($height*$target_size/$width);
                        }
                        else
                        {
                                $obj->height = $target_size;
                                $obj->width = round($width*$target_size/$height);
                        }
                }
                return $obj;
        }
	
	function autorotate(Imagick $image)
	{
    		switch ($image->getImageOrientation()) 
		{
    		case Imagick::ORIENTATION_TOPLEFT:
        		break;
    		case Imagick::ORIENTATION_TOPRIGHT:
        		$image->flopImage();
        		break;
    		case Imagick::ORIENTATION_BOTTOMRIGHT:
        		$image->rotateImage("#000", 180);
        		break;
    		case Imagick::ORIENTATION_BOTTOMLEFT:
        		$image->flopImage();
        		$image->rotateImage("#000", 180);
        		break;
    		case Imagick::ORIENTATION_LEFTTOP:
        		$image->flopImage();
        		$image->rotateImage("#000", -90);
        		break;
    		case Imagick::ORIENTATION_RIGHTTOP:
        		$image->rotateImage("#000", 90);
        		break;
    		case Imagick::ORIENTATION_RIGHTBOTTOM:
        		$image->flopImage();
        		$image->rotateImage("#000", 90);
        		break;
    		case Imagick::ORIENTATION_LEFTBOTTOM:
        		$image->rotateImage("#000", -90);
        		break;
    		default: // Invalid orientation
        		break;
    		}
   		 $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    		return $image;
	}

	function checkGroup($group)
        {
                if(!$group) return true;
                $nogroup = explode(";",$group);
                if(!count($nogroup)) return true;
                $logged_info = Context::get('logged_info');
                if(!$logged_info)  return true;
                foreach($nogroup as $egroup)
                {
                        if(array_key_exists($egroup,$logged_info->group_list)) return false;
                }
                return true;
        }
	
	private function returnExif($exif_data)
	{
	        $ExMode = array('Auto exposure','Manual exposure','Auto bracket');
        	$uni_pattern = '<span><b>%s</b>%s</span>';
	        $flashfired = ($exif_data['Flash'] & 1) != 0;
        	$Flash = array('Not Fired','Fired');
	        $Exposure = array('자동','수동','프로그램','조리개우선','셔터우선','정물사진모드','스포츠모드','인물사진모드','풍경사진모드');
        	$WB = array('Auto','Manual');
	        $Metering = array('','평균평가측광','중앙부중점측광','스팟측광','멀티스팟측광','패턴측광','부분측>광');

	        if(!$exif_data['Make']) $exif_data['Make'] = "Unknown";
        	if(!$exif_data['Model']) $exif_data['Model'] = "Unknown";
	        if($exif_data['DateTimeOriginal'] == $exif_data['DateTime'])
        	$exif_data['DateTime'] = null;
	        if($exif_data['DateTimeDigitized'] == '0000:00:00 00:00:00')
        	$exif_data['DateTimeOriginal'] = null;
	        if($exif_data['COMPUTED']['Width'] == $exif_data['ExifImageWidth'])
        	$exif_data['COMPUTED']['Width'] = null;
	        $replace = array(
        	        array('카메라제조사', $exif_data['Make']),
                	array('카메라모델명', $exif_data['Model']),
	                array('소프트웨어', $exif_data['Software'], substr($exif_data['Software'],0,30)),
        	        array('촬영일자', $exif_data['DateTimeOriginal']),
                	array('저장일자', $exif_data['DateTime']),
	                array('촬영자', $exif_data['Artist']),
        	        array('감도(ISO)', $exif_data['ISOSpeedRatings']),
                	array('촬영모드', $exif_data['ExposureProgram'], $Exposure[$exif_data['ExposureProgram']]),
	                array('노출모드', $exif_data['ExposureMode'], $ExMode[$exif_data['ExposureMode']]),
        	        array('측광모드', $exif_data['MeteringMode'], $Metering[$exif_data['MeteringMode']]),
                	array('노출시간', $exif_data['ExposureTime']),
	                array('조리개 값', $exif_data['COMPUTED']['ApertureFNumber']),
        	        array('촛점거리', $exif_data['FocalLength']),
                	array('조리개 최대개방', $exif_data['MaxApertureValue']),
	                array('노출보정', $exif_data['ExposureBiasValue'], $exif_data['ExposureBiasValue']),
        	        array('플래쉬', $flashfired, $Flash[$flashfired]),
                	array('35mm 환산', $exif_data['FocalLengthIn35mmFilm']),
	                array('화이트밸런스', $exif_data['WhiteBalance'], $WB[$exif_data['WhiteBalance']]),
        	        array('사진 크기', $exif_data['COMPUTED']['Width'], $exif_data['COMPUTED']['Width'].' X '.$exif_data['COMPUTED']['Height']),
                	array('원본사진 크기', $exif_data['ExifImageWidth'], $exif_data['ExifImageWidth'].' X '.$exif_data['ExifImageLength'])
	        );
	
        	$exif_info = array();
	        for($i=0;$i<count($replace);$i++)
        	{
                	if($replace[$i][2])
	                        $value = $replace[$i][2];
        	        else
                	        $value = $replace[$i][1];
	                if(isset($replace[$i][1]))
        	                $exif_info[] = sprintf($uni_pattern, $replace[$i][0], $value);
	        }
        	return $exif_info;
	}

	private function returnGps($exif_data)
	{
        	$uni_pattern = '<span><b>%s</b>%s</span>';
	        $latitude = $longitude = $altitude = $gps = null;
        	$exif_info = array();
	        if($exif_data['GPSLongitude'] && $exif_data['GPSLatitude'])
        	{
	                $latitude = sprintf($uni_pattern, '위도', $exif_data['GPSLatitudeRef'].' '.self::gps($exif_data["GPSLatitude"]));
        	        $longitude = sprintf($uni_pattern, '경도',$exif_data['GPSLongitudeRef'].' '.self::gps($exif_data['GPSLongitude']));
                	if($exif_data['GPSAltitude']) $altitude = sprintf( $uni_pattern, '고도', '해발 '.self::gpsaltitude($exif_data['GPSAltitude']).'m');
	                $gps = self::triphoto_getGPS($exif_data);
        	        $exif_info[] = sprintf('<span class="exif_gps" title="%s"><sub title="%s,%s"></sub>%s%s%s</span></span>','클릭하면 지도에 위치를 표시합니다',$gps['latitude'],$gps['longitude'],$latitude,$longitude,$altitude);
        		return $exif_info;
		}
		else return;
	}	

	function triphoto_getGPS($exif)
        {
                $LatM = 1; $LongM = 1;
                if($exif["GPSLatitudeRef"] == 'S')
                $LatM = -1;
                if($exif["GPSLongitudeRef"] == 'W')
                $LongM = -1;

                $gps['LatDegree']=$exif["GPSLatitude"][0];
                $gps['LatMinute']=$exif["GPSLatitude"][1];
                $gps['LatgSeconds']=$exif["GPSLatitude"][2];
                $gps['LongDegree']=$exif["GPSLongitude"][0];
                $gps['LongMinute']=$exif["GPSLongitude"][1];
                $gps['LongSeconds']=$exif["GPSLongitude"][2];
                foreach($gps as $key => $value)
                {
                        $pos = strpos($value, '/');
                        if($pos !== false)
                        {
                                $temp = explode('/',$value);
                                $gps[$key] = $temp[0] / $temp[1];
                        }
                }

                $result['latitude'] = $LatM * ($gps['LatDegree'] + ($gps['LatMinute'] / 60) + ($gps['LatgSeconds'] / 3600));
                $result['longitude'] = $LongM * ($gps['LongDegree'] + ($gps['LongMinute'] / 60) + ($gps['LongSeconds'] / 3600));

                return $result;
        }

        function gpsaltitude($coordinate)
        {
                if(!$coordinate) return false;
                $part = explode('/', $coordinate);
                if($part[0] == 0)
                        return 0;
                elseif($part[1] == 1 || count($part) ==1)
                        return $part[0];
                else
                        return number_format(floatval($part[0])/floatval($part[1]));
        }

        function gps($coordinate)
        {
                for ($i = 0; $i < 3; $i++)
                {
                        $part = explode('/', $coordinate[$i]);
                        if (count($part) == 1)
                        {
                                $coordinate[$i] = $part[0];
                        }
                        elseif(count($part) == 2)
                        {
                                $coordinate[$i] = floatval($part[0])/floatval($part[1]);
                        } else
                        {
                              $coordinate[$i] = 0;
                        }
                }
                list($degrees, $minutes, $seconds) = $coordinate;
                return $degrees.'.'.sprintf("%02s",$minutes).'.'.sprintf("%02s",$seconds);
        }

	function clearEXIF($file)
        {
		$image = new \Imagick($file);
		$image->setImageColorSpace(Imagick::COLORSPACE_SRGB);
                $profiles = $image->getImageProfiles("icc", true);
                $image->stripImage();
                if(!empty($profiles))   $image->profileImage("icc", $profiles['icc']);
		$image->setImageFormat("png");
                $image->writeimage($file);
                $image->clear();
        }


}
/* End of file imageprocess.model.php */
/* Location: ./modules/imageprocess/imageprocess.model.php */
