<?php
/**
 * @file   jp.lang.php
 * @author karma (http://www.wildgreen.co.kr) 翻訳：HIRO
 * @brief  imageprocess モジュールの日本語パッケージ
 **/

$lang->imageprocess = "イメージプロセシング";     

$lang->about_imageprocess = "サイズの大きいイメージファイルの大きさを減らしてウォーターマークを刻む機能をするモジュールです.";

$lang->change_kfile ='韓国語及び特殊文字<br />ファイル名前変更';
$lang->Resize_use = "イメージ縮小使用";
$lang->Resize_width = "イメージ最大サイズ";
//$lang->imageprocess_notice1 = "横,縦中幅が大きい方を基準で縮小します.";
$lang->about_image_mid = '使われる対象を指定することができます.<br />(すべてのチェックを解除すると全対象で使用可能です.)';
$lang->target_width = '基準軸';
$lang->target_width_Y ='横,縦の中で長い方を基準';
$lang->target_width_N ='横軸基準';

$lang->msg_target_width='横軸基準を選択した場合縦軸の大きさに構わず横軸が大きければ縮小します.';
$lang->cmd_resize_use = 'イメージ縮小設定';
$lang->cmd_watermark = 'ウォーターマーク設定';
$lang->cmd_original_store ='原本保存設定';
$lang->cmd_etc ='付加機能設定';
$lang->watermark_use = "ウォーターマーク機能使用";

$lang->original_store = '原本写真保存';
$lang->watermark = 'ウォーターマークファイル';
$lang->minimum_width ='最小大きさ';
$lang->resize_quality ='イメージ品質';
$lang->water_position ='ウォーターマーク位置';
$lang->original_format = "原本フォ―マット";
	$lang->trans_format = "変換フォ―マット";
	$lang->magic_conversion = "イメージ フォ―マット 変換";
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
        'RB' => '右側下端',
        'RT' => '右側上端',
        'LT' => '左側上端',
        'LB' => '左側下端',
		'SE' => '右側中下端',
		'NE' => '右側中上端',
		'NW' => '左側中上端',
		'SW' => '左側中下端',
		'CE' => '真中',
    );

$lang->original_format_type = array(
        'bmp' => 'BMP',
        'tiff' => 'TIFF',
        'psd' => 'PSD',
        'eps' => 'EPS',
		'xcf' => 'XCF',
		'tga' => 'TGA',
		//'raw' => 'RAWフォ―マット',
    );
	//$lang->about_raw_format = "RAW フォ―マット: Sony(ARW),Fuji(RAF),Olympus(ORF),Cannon(CRW,CR2),Adobe(DNG),Pentax(PEF),Minolta(MRW),Sigam(X3F),Nicon(NEF)";

$lang->ext_type = array('jpg','jpeg','png');
$lang->msg_watermark_type ="適用対象ファイル種類";
$lang->store_path ='原本保存フォルダ';
$lang->down_group = '原本ダウンロード許容';
$lang->notpng = 'png ファイルではありません';
$lang->magic_use ='Imagemagick 設定';
$lang->magic_path ='Imagemagick path';
$lang->magic ='ImageMagick使用';
$lang->gd ='GD使用';
$lang->input_magic_path = 'ImageMagick pathを入力してください.';
$lang->check_magic_path = 'ImageMagick 実行ファイルが確認されません.';

$lang->msg_magic_use ='ImageMagick이 設置された場合のみ変更してください.';
$lang->msg_magic_path ='ImageMagick 実行ファイルがあるフォルダを入力してください. ex) /usr/local/bin/ (* pathが設定されている場合には入力しなくても良いです.)';
$lang->msg_down_group = 'チェックしなければ本人のみ原本ダウンロードが可能です. 他のグループには操作された写真がダウンロードされます.';
$lang->checkfolder ='保存フォルダを確認してください.';
$lang->msg_store_path2 ='絶対経路を入力なさらなければなりません. 現在 Xeの設置された経路は次の通りです.<br />';
$lang->msg_store_path ='permissionを 707で修正しなければなりません. 入力しなければ既存フォルダに保存します.<br />なるべくウェブ接近ができない経路を勧奨します. ex) ';
$lang->msg_watermark_use ='ウォーターマーク機能使用可否を設定します.';
$lang->msg_water_position ='ウォーターマークを刻む位置を設定します.';
$lang->msg_Resize_use ='使用を選択すれば写真を下のイメージ最大大きさ以上のファイルは縮小保存します. 選択しなくてもウォーターマーク機能は別に作動します.';
$lang->msg_change_kfile = '韓国語及び特殊文字のファイル名前を任意の数字に変更させます.<br /> (使用を選択した場合イメージファイルのみならずすべての拡張子のファイルに適用されます.)';
$lang->msg_resize_quality = '100以下の数字を入力します. 数字が小さいほどファイルの用量は減るが写真品質は落ちます.写真の品質を維持しようとすれば 100を入力してください. 基本は 80ですイメージ縮小機能を使う場合のみ作動します';
$lang->msg_minimum_width ='横,縦中一つでも最小大きさより小さな大きさの写真はウォーターマークを操作しません.';
$lang->msg_original_store = '写真を操作する前の原本写真を保存します. 縮小やウォーターマーク等の操作をしたファイルのみ保存します';
$lang->msg_watermark = 'ファイルを指定しなければ ./modules/imageprocess/stamp/stamp.pngに設定されます.';
$lang->msg_rotate_use ='Image automatially rotate upright position during upload mobile images.';
$lang->rotate_use ='AutoRotation of mobile images';
?>
