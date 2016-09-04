<?php

//注意，引用此文件之前，需要先引用wordpress的wp-load.php文件，例如：
//require( dirname(__FILE__) . WP_DIR .'/wp-load.php' );   

if(!defined('SHOW_DEBUG_INFO'))
  define('SHOW_DEBUG_INFO',0);


class WXPRESS{


  static public function wx_oauth($code){

    //1，微信回传来的$code
    WXPRESS::dump($code,'code');

    //2, 根据code，换回 access_token 
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $GLOBALS['wp_cfg']['WX_APPID'] . '&secret=' . $GLOBALS['wp_cfg']['WX_APPSECRET'] . '&code=' . $code . '&grant_type=authorization_code';
    WXPRESS::dump($GLOBALS['wp_cfg'],'GLOBALS.cfg');
    $json_token = json_decode(file_get_contents($url),true);
    WXPRESS::dump($json_token,'json_token');

    //3, 获取用户信息
    $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $json_token['access_token'] . '&openid=' . $json_token['openid'];
    $user_info = json_decode(file_get_contents($info_url),true);
    WXPRESS::dump($user_info,'user_info');

    $weixin_id = $user_info['unionid'];
    if(!$weixin_id) wp_die('Error get access_token.');

    $v=WXPRESS::wx_login($user_info);
    WXPRESS::dump($v);
    WXPRESS::home();
    
}

/*
1 通过usermeta查一下当前的微信用户曾经扫描登录过没
2 扫描过：
   扫描过的用户为 $oauth_user
   
   2.1 是否有已登录wp用户   
        且该用户的UID不同
           【登出wp用户】
	 2.2【登录$oauth_user用户，并更新用户信息】【完成】
   


3 第一次扫
  3.1 有已登录wp用户
     该用户没有绑定UID
        【绑定UID】【完成】
	   该用户有绑定UID
        【肯定是不同的UID，故登出wp用户】
  3.2 未登录
  【新建】
*/
  static public function wx_login($user_info){
  //unionid, openid, nickname, headimgurl 
  //sex city province country
  
    if(!isset($user_info['unionid']))
      return WXPRESS::msg(101,'missing unionid');
    if(!isset($user_info['nickname']))
      return WXPRESS::msg(102,'missing nickname');
    $weixin_id = $user_info['unionid'];
    
    //1 通过usermeta查一下当前的微信用户曾经扫描登录过没
    $oauth_user = get_users(array('meta_key'=>WX_KEY,'meta_value'=>$weixin_id));

    WXPRESS::dump($oauth_user,'1 通过usermeta查一下当前的微信用户曾经扫描登录过没');
    //2 如果此UID有扫描登录过的记录
    if(!is_wp_error($oauth_user) && count($oauth_user)){    
      //2.1 目前已有登录用户
      if(is_user_logged_in()){
        WXPRESS::dump('2.1 目前已有登录用户');
        $this_user = wp_get_current_user();
        $ouid=get_user_meta($this_user->ID,WX_KEY,true);
        WXPRESS::dump($ouid,'ouid');

        //2.1.1 目前登录用户的微信UID不同，把当前登录用户登出，避免扫描绑定到到不同的用户去
        if($ouid !== $weixin_id) {
          WXPRESS::dump('2.1.1目前登录用户的微信UID不同，已登出');
          wp_logout();
        }
        //这样，第2.1过后，要么就是未登录用户，要么登录用户同微信扫描用户
      }

      //2.2登录为当前微信扫描用户
      WXPRESS::dump('2.2登录为当前微信扫描用户');
      wp_set_auth_cookie($oauth_user[0]->ID);
      WXPRESS::wx_savemeta($user_info,$oauth_user[0]->ID);
      return WXPRESS::msg(0,'OK1. (uid='.$oauth_user[0]->ID.')原有绑定用户.');

    }
    //3 如果没扫描登录过的记录（第一次扫描登录）
    else {    
      //3.1, 已有登录用户
      if(is_user_logged_in()){
        WXPRESS::dump('3.1, 已登录用户，第一次扫描登录时');
        $this_user = wp_get_current_user();
        $ouid=get_user_meta($this_user->ID,WX_KEY,true);
        WXPRESS::dump($ouid,'ouid');

        //3.1.1 目前登录用户 未绑定微信UID
        //绑定之，记录微信信息到当前用户的usermeta中
        if( '' == $ouid ) {
          WXPRESS::dump('3.1.1, 目前登录用户 未绑定微信UID');
          WXPRESS::wx_savemeta($user_info,$this_user->ID);
          return WXPRESS::msg(0,'OK2. (uid='.$this_user->ID.')新绑定到旧wp用户');
        }
        
        //3.1.2 目前登录用户 已绑定了微信UID, 则肯定是不同的UID
        wp_logout();
      }
      WXPRESS::dump('3.2 开始新建用户');
      $user_id = WXPRESS::new_user($user_info);
      if(is_wp_error($user_id)){
        //wp_die('Error create user.');
        return WXPRESS::msg(1003,'Error create user.');
      }
      //wp_signon(array('user_login'=>$login_name,'user_password'=>$random_password),false);
      wp_set_auth_cookie($user_id);

      WXPRESS::wx_savemeta($user_info,$user_id);
      return WXPRESS::msg(0,'OK3. (uid='.$user_id.')新用户绑定到新建wp用户成功');
      
    }
	}
  
  //===============================
  //以下为辅助函数
  
	static public function home(){
		$url = home_url();
    WXPRESS::dump($url,'home url');
		wp_redirect( $url );
		exit;
	}
  
	static public function dump($v,$info=''){
    if(! SHOW_DEBUG_INFO ) return;
    echo "<hr/><h3>$info</h3><pre>";var_dump($v);echo '</pre>';
  }
  
	static public function msg($c=0,$msg='Ok.'){
   return ['err_code'=>$c,'err_msg'=>$msg];
  }
  
  static public function new_user($user_info){
    $username=$user_info['nickname'];
    $login_name = 'wx' . wp_create_nonce($user_info['unionid']) . time();
    $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
    $userdata=array(
        'user_login' => $login_name,
        'display_name' => $username,
        'user_pass' => $random_password,
        'user_email'=>$user_info['unionid']. '@wechat-qgs-auto.info',
        'nick_name' => $username
    );
    
    WXPRESS::dump($userdata,'userdata');
    $user_id = wp_insert_user( $userdata );
    return $user_id;
  }
  
  static public function wx_savemeta($user_info,$user_id){
    update_usermeta($user_id ,WX_KEY,$user_info['unionid']);//必须有
    if(isset($user_info['openid']))    update_usermeta($user_id ,'weixin_openid',$user_info['openid']);
    if(isset($user_info['headimgurl']))update_usermeta($user_id ,'weixin_avatar',$user_info['headimgurl']);
    //if(isset($user_info['sex']))       update_usermeta($user_id ,'weixin_sex',   $user_info['sex']);
    //if(isset($user_info['city']))      update_usermeta($user_id ,'weixin_city',   $user_info['city']);
    //if(isset($user_info['province']))  update_usermeta($user_id ,'weixin_province',$user_info['province']);
    //if(isset($user_info['country']))   update_usermeta($user_id ,'weixin_country',$user_info['country']);
  }
  

}



