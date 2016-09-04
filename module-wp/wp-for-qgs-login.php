<?php
require_once( $GLOBALS['wp_cfg']['WP_PATH'] . '/wp-load.php' ); 
require_once ( 'src/wxpress.php' );  

 
//根据请高手公众号的用户信息，登录wordpress
/*
  TODO:
  目前如果是先关注帐号，用此函数登录，是可以生成用户并登录
  但后面再关注后再来登录，暂时未处理关联到原先帐号的功能
*/
/**
  $user_info['openid']一定要有。
  $user_info['unionid'] 
    是用来识别用户的。
    如果未关注的人没有此项，wp数据里就先设为：'QGSOID-'.$user_info['openid'];
  $user_info['nickname']
    是wp里的显示名字。
    如果未关注的人没有此项，wp数据里就先设为：'QGS-'.time();
  $user_info['headimgurl']
    是wp里的显示头像地址。
    如果未关注的人没有此项，就先空着。
    
    
  返回值：
    return ['err_code'=>c$ode,'err_msg'=>$msg];
  }
  */
function qgs_user_login($user_info){
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