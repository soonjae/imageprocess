<?php
/**
 * @file   ko.lang.php
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  english language of imageprocess module
 **/

$lang->imageprocess = "imageprocess";     

$lang->about_imageprocess = "This is module for resizing and printing wtarmark  of image files, It can make store original file before processing";

$lang->change_kfile ='Change 2-byte filename';
$lang->Resize_use = "Use resizing";
$lang->Resize_width = "maximum image size";
$lang->imageprocess_notice1 = "whether width or height, the bigger size will be resized to this size.";
$lang->about_image_mid = "module can select targets.<br />(All targets will be selected when nothing is selected)";
$lang->target_width = 'basis of resize';
$lang->target_width_Y ='long axis';
$lang->target_width_N ='width';

$lang->cmd_resize_use = 'Config Resizing';
$lang->cmd_watermark = 'Config watermark';
$lang->cmd_original_store ='Config original file storage';
$lang->cmd_etc ='ExtraConfig';
$lang->watermark_use = "Use watermark";

$lang->original_store = 'Store original file';
$lang->watermark = 'Watermark';
$lang->minimum_width ='minimum width';
$lang->resize_quality ='quality';
$lang->water_position ='wtarmark position';
$lang->original_format = "Original Format";
$lang->trans_format = "Transformat";
$lang->magic_conversion = "Image Conversion";
$lang->image_types = array(
        1 => 'GIF',
        2 => 'JPG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF(intel byte order)',
        8 => 'TIFF(motorola byte order)',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM'
    );
$lang->watermark_type = array(
        'RB' => 'BottomRight',
        'RT' => 'TopRight',
        'LT' => 'TopLeft',
        'LB' => 'BottomLeft',
		'SE' => 'RightLowerMiddle',
		'NE' => 'RightUpperMiddle',
		'NW' => 'LeftUpperMiddle',
		'SW' => 'LeftLowerMiddle',
		'CE' => 'Center',
    );
$lang->original_format_type = array(
        'bmp' => 'BMP',
        'tiff' => 'TIFF',
        'psd' => 'PSD',
        'eps' => 'EPS',
		'xcf' => 'XCF',
		'tga' => 'TGA',
		//'raw' => 'RAW Foramt',
    );
	//$lang->about_raw_format = "RAW Format: Sony(ARW),Fuji(RAF),Olympus(ORF),Cannon(CRW,CR2),Adobe(DNG),Pentax(PEF),Minolta(MRW),Sigam(X3F),Nicon(NEF)";

$lang->ext_type = array('jpg','jpeg','png');
$lang->msg_watermark_type ="Select file type";
$lang->store_path ='Original file strage folder';
$lang->down_group = 'Grant download files';
$lang->notpng = 'not png format';
$lang->magic_use ='use Imagemagick';
$lang->magic_path ='Imagemagick path';
$lang->magic ='use ImageMagick';
$lang->gd ='use GD';
$lang->input_magic_path = 'Please input ImageMagick path.';
$lang->check_magic_path = 'No validate ImageMagick execution file ';

$lang->msg_magic_use ='It cannot be worked if ImageMagick is  not installed.';
$lang->msg_magic_path ='Please input ImageMagick path. ex) /usr/local/bin/ ';
$lang->msg_down_group = 'If not checked, only the author can download the original files. For othter groups can download processed images.';
$lang->checkfolder ='folder not permitted';
$lang->msg_store_path2 ="Please input the location to store original files<br />Both absolute path such as '/path1/path2/sample.php' or relative path such as '../path2/sample.php' can be used.<br /><br />This is current XE's absolute path.<br />";
$lang->msg_store_path ='Permission must be 707. If you donot input the path the original files will be stored with the processed images.<br />If possible, please input the location which cannot be accesed by web. ex) ';
$lang->msg_watermark_use ='Select whether to use printing watermark';
$lang->msg_water_position ='Select the location of printing watermark.';
$lang->msg_Resize_use ='Select whether to use resizing or not';
$lang->msg_change_kfile = 'This function is changing 2-byte code filenames to random numeric filenames';
$lang->msg_resize_quality = 'Please input quality of image files. 100 is maximum';
$lang->msg_minimum_width ='Image files smaller than this size will not be printed watermark.';
$lang->msg_original_store = 'This is for storing original files before processing.';
$lang->msg_watermark = "'./modules/imageprocess/stamp/stamp.png' will be default.";
$lang->msg_rotate_use ='Image automatially rotate upright position during upload mobile images.';
$lang->rotate_use ='AutoRotation of mobile images';
?>
