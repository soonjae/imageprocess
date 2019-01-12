<?php
/**
 * @file   ko.lang.php
 * @author karma (http://www.wildgreen.co.kr)
 * @brief  imageprocess 모듈의 기본 언어
 **/

$lang->nogroup = "제외시킬 그룹";
$lang->msg_nogroup ="체크된 그룹의 회원이 업로드한 이미지에는 기능을 적용하지 않습니다.";
$lang->cmd_textlogo = "텍스트로고";
$lang->imageprocess = "이미지 프로세스 모듈";     
$lang->msg_rotate_no_use = "PHP EXIF extension이 설치되지 않아서 이미지 로테이션 기능을 사용할 수 없습니다.";
$lang->about_imageprocess = "사이즈가 큰 이미지 화일의 크기를 줄이고 워터마크를 새기는 기능을 하는 모듈입니다.";
$lang->ip_safe_mode ="safe_mode 설정으로 이 서버에서는 ImageMagick의 사용이 불가능합니다.";
$lang->mgaic_installed ="에 imageMagick이 설치되어있습니다.";

//$lang->change_kfile ='한글및특수문자화일이름변경';
$lang->Resize_use = "이미지 축소 사용";
$lang->Resize_width = "이미지 최대 사이즈";
$lang->about_image_mid = '사용될 대상을 지정할 수 있습니다.<br />(모두 해제 시 모든 대상에서 사용 가능하지만 개별설정은 적용되지 않습니다.)';
$lang->target_width = '기준축';
$lang->target_width_Y ='가로세로중긴쪽기준';
$lang->target_width_N ='가로축기준';

$lang->msg_target_width='가로축기준을 선택한 경우 세로축의 크기에 상관없이 가로축이 크면 축소합니다.';
$lang->cmd_resize_use = '이미지 축소 설정';
$lang->cmd_watermark = '워터마크 설정';
$lang->cmd_original_store ='원본저장 설정';
$lang->cmd_etc ='부가기능설정';
$lang->watermark_use = "워터마크 기능 사용";
$lang->about_watermark_use ="워터마크는 각자 사이트에 맞게 제작해야하며, 워터마크 사용시 화질이 현저히 나빠질 수 있기 때문에 특히 GD를 사용하는 경우 권장하지 않습니다.";
$lang->water_margin = "마진설정";
$lang->xmargin = "가로축";
$lang->ymargin = "세로축";
$lang->msg_water_margin = "워터마크 위치를 가장자리에서 이동해야하는 경우 입력하십시요. 기본은 가로, 세로 10px입니다.";
$lang->original_store = '원본사진 저장';
$lang->watermark = 'Watermark 화일';
$lang->minimum_width ='최소크기';
$lang->resize_quality ='사진품질';
$lang->water_position ='워터마크 위치';
$lang->each_water_margin = "개별마진설정";
$lang->each_water_position ='개별워터마크 위치';
$lang->each_watermark = '개별 Watermark 화일';
$lang->each_setup = ' * 개별설정을 하지 않으면 메인설정값이 적용됩니다.';

$lang->original_format = "원본포맷";
$lang->trans_format = "변환포맷";
	
$lang->watermark_type = array(	'RB' => '우측하단',	'RT' => '우측상단',	'LT' => '좌측상단',	'LB' => '좌측하단',	'SE' => '우측중하단',	'NE' => '우측중상단',	'NW' =>'좌측중상단',	'SW' => '좌측중하단',	'CE' => '정중앙',);
$lang->original_format_type = array(	'bmp' => 'BMP',	'tiff' => 'TIFF','tga' => 'TGA', 'psd' => 'PSD', 'raw' => 'RAW포맷 지원',);
$lang->about_raw_format = "PSD의 경우 포토샵 CS5에서는 compatibility를 최대로해서 저장하지 않으면 일부 서버에서는 깨질수 있습니다.";
$lang->ext_type = array('jpg','jpeg','png'); 
$lang->abount_target_watermark ="  (gif 포맷은 지원하지 않습니다.)";
$lang->msg_watermark_type ="적용대상 화일종류";
$lang->water_quality="워터마크 품질";
$lang->msg_water_quality ="압축비율을 입력하십시요. 기본은 100이며 숫자가 낮을수록 화일용량은 작아지지만 화질은 떨어집니다.<br/>리사이즈기능과 동시에 사용하면서 리사이즈 quality를 많이 낮춘경우 100으로 유지하는 것이 좋습니다.";
$lang->store_path ='원본저장폴더';
$lang->down_group = '원본 다운로드허용';
$lang->notpng = 'png 화일이 아닙니다';
$lang->magic_use ='Imagemagick 설정';
$lang->magic_path ='Imagemagick path';
$lang->magic ='ImageMagick사용';
$lang->gd ='GD사용';
$lang->input_magic_path = 'ImageMagick path를 입력하십시요.';
$lang->check_magic_path = 'ImageMagick 실행화일이 확인되지 않습니다.';
$lang->magic_conversion = "이미지 포맷 변환";
$lang->msg_magic_conversion="ImageMagick을 사용하는 경우에만 지원합니다. imagemagick을 선택하는 경우 bmp, tiff 등의 화일을 선택된 포맷으로 변환시켜 저장해주는 기능입니다.";
$lang->msg_magic_use ='ImageMagick이 설치된 경우에만 변경하십시요.';
$lang->msg_magic_path ='ImageMagick 실행화일이 있는 폴더를 입력하십시요. ex) /usr/local/bin/ (* path가 설정되어있는 경우에는 입력하지 않으셔도 됩니다.)';
$lang->msg_down_group = '체크하지않으면 본인만 원본다운로드가 가능합니다. 다른그룹에게는 조작된 사진이 다운로드됩니다.';
$lang->checkfolder ='저장폴더를 다시 확인해보시기 바랍니다.';
$lang->msg_store_path2 ='절대경로를 입력하셔야합니다. 현재 Xe가 설치된 경로는 다음과 같습니다.<br />';
$lang->msg_store_path ='permission을 707로 수정해야합니다. 입력하지않으면 기존폴더에 저장합니다.<br />가급적 웹접근이 안되는 경로를 권장합니다. ex) ';
$lang->msg_watermark_use ='워터마크 기능 사용 여부를 설정합니다.';
$lang->msg_water_position ='워터마크를 새겨넣을 위치를 설정합니다.';
$lang->msg_Resize_use ='사용을 선택하면 사진을 아래 이미지 최대크기 이상인 화일은 축소저장합니다. 선택하지 않아도 워터마크기능은 별도로 동작합니다.';
$lang->msg_resize_quality = '100이하의 숫자를 입력합니다. 숫자가 작을수록 화일의 용량은 줄어들지만 사진품질은 떨어집니다.사진의 품질을 유지하려면 100을 입력하십시요. 기본은 80 이며 이미지축소기능을 사용하는 경우만 작동합니다';
$lang->msg_minimum_width ='가로,세로중 하나라도 최소크기보다 작은 크기의 사진은 조작하지 않습니다.';
$lang->msg_original_store = '사진을 조작하기 전의 원본 사진을 저장합니다. 축소나 워터마크등의 조작을 한 화일만 저장합니다';
$lang->msg_watermark = '화일을 지정하지 않으면 ./modules/imageprocess/stamp/stamp.png로 설정됩니다.';
$lang->msg_rotate_use ='모바일 기능을 사용시 사진의 방향을 자동으로 회전시켜서 저장하는 기능입니다.';
$lang->rotate_use ='자동회전기능';
$lang->module_imageprocess = '이미지프로세스 기본모듈';
$lang->update_imageprocess = '이미지프로세스 모듈이 설치되어 있지 않거나 버전이 너무 낮습니
다. 이미지프로세스 모듈의 최신버전을 설치후 사용하시기 바랍니다. <br /><a href="http://www.xpressengine.com/index.php?mid=download&package_srl=18728678" target="_blank">http://www.xpressengine.com/index.php?mid=download&package_srl=18728678</a><br />최신버전을 설치했음에도 이 메시지가 보이신다면 관리자페이지의 모듈항목에서 업데이트를 클릭후 사용하시기 바랍니다.';
//여기부터 텍스트로고 추가
$lang->cmd_textlogo = '텍스트로고 설정';
$lang->textlogo_use ='텍스트로고 사용';
$lang->msg_textlogo_use = '텍스트로고 기능 사용 여부를 설정합니다.';
$lang->logo_quality = '로고 품질';
$lang->logo_position = '텍스트로고위치';
$lang->msg_logo_position ='로고를 새겨넣을 위치를 설정합니다.';
$lang->textlogo = '로고문구';
$lang->msg_textlogo = '사진에 새길 로고문구를 입력하십시요.<br />%user_name% : 이름<br />%nick_name% : 닉네임<br />%user_id% :아이디<br /> %time% : 업로드날짜<br />%email_address% : >이메일, 등의 변수를 사용할 수 있습니다.,<br /> ex) http://mysite.com 마이사이트 photo by %nick_name%(%email_address%) on %time%';
$lang->logo_font ='사용할 폰트';
$lang->fontname_open ='폰트이름펼치기/숨기기';
$lang->logo_point = '글자의 크기';
$lang->logo_style = '로고 스타일';
$lang->logo_style_type = array(
    'simple' => '단순텍스트',
    'shadow' => '새도우',
    'stroke' => '외곽선',
);
$lang->about_font_style = '단순텍스트를 사용하는 경우 사진의 배경이 글자색과 비슷한 곳에서>는 글자가 보이지 않습니다. 단순텍스트 스타일은 특별한 경우에만 이용하시기 바랍니다. 기본은 새도우입니다.';
$lang->logo_font_type = '사용할 폰트';
$lang->about_font ='폰트를 선택하십시요. 폰트에 관한 자세한 설명은 <a href="http://heiswed.tistory.com/entry/Commercial-Free-Fonts" target="_blank">http://heiswed.tistory.com/entry/Commercial-Free-Fonts</a>를 참조하시기 바랍니다. ';
$lang->about_one_font = '폰트를 선택하십시요. <br />배포화일에는 화일사이즈의 문제로 기본적
으로 한개의 폰트만 내장되어 있습니다. 폰트의 추가는 http://cdn.naver.com/naver/NanumFont/fontfiles/NanumFont_TTF_ALL.zip 에서 화일을 다운로드받아서 ./modules/imageprocess/font 폴더에 복사해넣으시면 됩니다';

$lang->logo_position_type = array(
    'southeast' => '우측하단',
    'south' => '하단',
    'northeast' => '우측상단',
    'northwest' => '좌측상단',
    'north' => '상단',
    'southwest' => '좌측하단',
    'center' => '정중앙',
);
$lang->cmd_logo_color = '로고색상';
$lang->cmd_logo_fg = '글자색';
$lang->cmd_logo_bg = '글자배경색';
$lang->msg_logo_fg = '* 새겨넣을 글자의 색상을 선택하십시요.';
$lang->msg_logo_bg = '* 글자의 배경색을 선택하십시요.';
$lang->install_font = '폰트가 설치되어있지 않습니다.<br /><a href="http://cdn.naver.com/naver/NanumFont/fontfiles/NanumFont_TTF_ALL.zip">http://cdn.naver.com/naver/NanumFont/fontfiles/NanumFont_TTF_ALL.zip</a><br /><a href="http://kldp.net/frs/download.php/4695/un-fonts-core-1.0.2-080608.tar.gz">http://kldp.net/frs/download.php/4695/un-fonts-core-1.0.2-080608.tar.gz</a>i<br /><a href="http://kldp.net/projects/baekmuk/download/1429?filename=baekmuk-ttf-2.2.tar.gz">http://kldp.net/projects/baekmuk/download/1429?filename=baekmuk-ttf-2.2.tar.gz</a><br />등에서 폰트를 다운받으신후 ./modules/imageprocess/font 폴더에 복사해 넣으시면 됩니다.<br/><br />기타의 폰트는 서버에 따라 정상적인 작동을 하지않을 수 있습니다. <br />폰트에 관한 자세한 내용은 <a href="http://heiswed.tistory.com/entry/Commercial-Free-Fonts" target="_blank">http://heiswed.tistory.com/entry/Commercial-Free-Fonts</a>를 참조하시기 바랍니다.';
$exfont = '폰트';
$lang->msg_logo_color = '한가지 색상만 사용하는 경우 사진의 색상과 겹치는부분은 글자가 잘 >보이지않게 됩니다. 따라서 콘트라스트가 보색계통의 색상을 배경색으로 지쟁해서 사용하시는 것>이 좋습니다.';
