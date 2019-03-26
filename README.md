# imageprocess
imageprocess module for xpressengine and rhymix
starting verion : 2.1.1.1 on 2016-06-15
present version : 2.6.3 on 2019-03-25

변경사항
1. 그동안 누적된 많은 버그를 수정했습니다.
   php 버전 7.x에 대응했습니다.
   
2. 텍스트로고의 색상을 선택하는 방식을 바꿨습니다.
  코드를 입력하능 방식이 아니고 마우스로 색상을 선택하는 방식으로 변경했습니다.
  
3. 설정을 각 모듈설정에서 하게 수정
  각 모듈의 설정-추가설정 항목에서 각각의 기능을 통제할 수 있습니다.
  
4. imagick을 지원합니다.
  그동안 GD와 외부실행파일인 Imagamagick만을 지원했으나 PHP 내부 extesnsion중의 하나인 imagick을 지원합니다.
 
5. 불필요한 기능을 제거했습니다.
   포맷변환과 로고설정의 화질관리는 그 효용성이 크지 않아서 제거했습니다.
   
   
   기타 설정방법은 https://soonj.net/relfeed/181038 
