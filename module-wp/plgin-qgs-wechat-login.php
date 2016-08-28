<?php
/*
Plugin Name: qgs-wechat-login
Plugin URI: http://linjp.com/?wp-plugins=qgs-wechat-login
Description: Wechat login.
Version: 0.0.3
Author: linjp
Author URI: http://linjp.com
Text Domain: qgs-wechat-login
Domain Path: /languages
*/

/*  Copyright 2016 linjp  (email : pub@linjp.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'APPID' , 'wxfd48b9fdd7288012');
define('WX_KEY','weixin_unionid');

/*
  function custom_login_message() {
    echo '';
  }
  add_action('login_form', 'custom_login_message');

*/

//自定义登录界面LOGO链接
function custom_loginlogo_url($url) {
	return get_bloginfo('url'); //修改URL地址
}
add_filter( 'login_headerurl', 'custom_loginlogo_url' );

//自定义登录页面的LOGO图片
function my_custom_login_logo() {
    echo '<style type="text/css">
    #login {  width: 348px; }
        h1 { display: none !important; }
    </style> 
    
    
    
    <style>
      .my-hidden-item { display:"none" !important;}
      .login form {
        margin-top: 0px;
      }
      .my-tab-cont {
        margin-top: 10px;
      }
      .my-tab {
        cursor: pointer; 
        display: block;
        height: 50px;
        line-height:48px;
        width: 46%;
        margin: 0 2px;
        float: left;
        text-align: center;
         
        border-left: 1px solid #b4b9be;
        border-top: 1px solid #b4b9be;
        border-right: 1px solid #b4b9be;
      }
      .my-tab-active {
        background: #fff;
        border-left: 1px solid #999;
        border-top: 1px solid #999;
        border-right: 1px solid #999;
      }
    </style>
      <script src="http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
      
      <script>
      console.log("QGS plugin loaded. OK. 41");
        //把登录窗口修改掉
      document.addEventListener("DOMContentLoaded",function(){
        loginform=document.getElementById("loginform");
        var tab1= document.createElement("div");
        var ts1= document.createElement("div");
        tab1.className="my-tab-cont";
        ts1.id="ts1-wx";
        ts1.className="my-tab my-tab-active";
        ts1.innerHTML="微信登录";
        ts1.onclick=function(){sel_wx_login(1)};
        tab1.appendChild(ts1);
        var ts1= document.createElement("div");
        ts1.id="ts1-userid";
        ts1.className="my-tab";
        ts1.innerHTML="用户密码登录";
        ts1.onclick=function(){sel_wx_login(0)};
        tab1.appendChild(ts1);
        
        wx_log= document.createElement("div");
        wx_log.id="login_container_1";
        loginform.parentElement.insertBefore(tab1,loginform);
        //loginform.parentElement.appendChild(tab1);
       
        loginform.insertBefore(wx_log,loginform.children[0]);

        var obj = new WxLogin({
            id:"login_container_1", 
            appid: "'. APPID   .'", 
            scope: "snsapi_login", 
            
            redirect_uri: "' . 
            urlencode(get_bloginfo("url")) .
            '", 
            state: "123",
            style: "",
            href: ""
          });
      });
      </script>
      <script>
      document.addEventListener("DOMContentLoaded",function(){
          sel_wx_login(1);
      });
          function sel_wx_login(wx_param) {
            if ( typeof (sel_wx_login.w)===undefined  ){
              sel_wx_login.w=true;
            }
            
            if(wx_param === undefined)sel_wx_login.w=!sel_wx_login.w;
            else sel_wx_login.w=wx_param;
            is_wx=sel_wx_login.w;
            
            wx_ele=is_wx?"block":"none";
            not_wx_ele=!is_wx?"block":"none";

            loginform=document.getElementById("loginform");
            n=loginform.children.length;
            loginform.children[0].style.display = wx_ele;
            for(i=1;i<n;i++) {
              loginform.children[i].style.display = not_wx_ele;
            }
            is_wx ? document.getElementById("ts1-wx").classList.add("my-tab-active")
            : document.getElementById("ts1-userid").classList.add("my-tab-active") ;
            !is_wx ? document.getElementById("ts1-wx").classList.remove("my-tab-active")
            : document.getElementById("ts1-userid").classList.remove("my-tab-active") ;
          }
          
          
      </script>
    
    
    ';
  
}
add_action('login_head', 'my_custom_login_logo');

function qgs_widget_loginstat_disp($args) {
  echo $args['before_widget'];
  echo $args['before_title'] . '高手帐户' .  $args['after_title'];
  // Print some HTML for the widget to display here.
  $wx_login_str = '
      <div id="login_container_3"></div>
      <script src="http://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
      <script>
        var obj = new WxLogin({
            id:"login_container_3", 
            appid: "'. APPID   .'", 
            scope: "snsapi_login", 
            
            redirect_uri: "' . 
            urlencode(get_bloginfo("url")) .
            '", 
            state: "123",
            style: "",
            href: ""
          });
      </script>
  ';
  $umeta=[];
  if(is_user_logged_in()) {
    $this_user = wp_get_current_user();
    $umeta=get_user_meta($this_user->ID);
  }
  if(isset($umeta[WX_KEY])){//已登录，且是绑定微信的
    echo "欢迎您，".$this_user->display_name."！<br/>";
    wp_loginout();
    echo "<img src='{$umeta[weixin_avatar][0]}'/>";
    //echo 'ouid:<pre>';var_dump($this_user);echo '<hr/>';var_dump($umeta);echo '</pre>';
  } else { 
    echo $wx_login_str;
  }
  echo $args['after_widget'];
}

function qgs_widget_loginstat_new() {
  $id='qgs-login-stat';
  $name='高手帐户';
  $options=[
  
    'description' => '请高手平台帐户面板'];
    
    
  wp_register_sidebar_widget(
      $id, 
      $name, 
      qgs_widget_loginstat_disp, 
      $options
  ); 
}
qgs_widget_loginstat_new();

//echo '<h1>WWOWOWOW</h1>';

/*

plugin_dir_path()
plugins_url()

*/


?>
