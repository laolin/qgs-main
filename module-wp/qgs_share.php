<?php
require_once( $GLOBALS['wp_cfg']['WP_PATH'] . '/wp-load.php' ); 
require_once ( 'src/wxpress.php' );  

 
//根据请高手公众号的用户信息，登录wordpress
/*wp模块提供PHP接口：
  1. 调用函数：loginByUserinfo($user_info);
     然后就已登录wp
     即使未曾扫描过wp的微信第三方二维码，也要建立一个用户。
    返回值：
      return ['err_code'=>$code,'err_msg'=>$msg];
      err_code==0表示正确。
      
  2. checkLoginUnionid();
     如果已登录wp：
       返回unionid字符串
     否则：
       返回""
       
  */
class QgsShare{

  static public function loginByUnoinid($unionid){
    return QgsShare::loginByUserinfo([
        'openid'=>'QGSOID-'.time(),
        'unionid'=>$unionid
      ]);
  }
  
  static public function loginByUserinfo($user_info){
    if(!isset($user_info['openid'])) {
      return WXPRESS::msg(2001,'Missing openid.');
    }
    $user_info['openid']='QGS-'.$user_info['openid'];
    if(!isset($user_info['unionid'])) {
      $user_info['unionid']='QGSOID-'.$user_info['openid'];
    }
    if(!isset($user_info['nickname'])) {
      $user_info['nickname']='QGS-'.time();
    }
    return WXPRESS::wx_login($user_info);
  }
  
  static public function checkLoginUnionid(){
    $ouid='';
    if(is_user_logged_in()) {
        $this_user = wp_get_current_user();
        $ouid=get_user_meta($this_user->ID,WX_KEY,true);
    }
    return $ouid;
  }
  
}