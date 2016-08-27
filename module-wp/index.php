<?php


$GLOBALS['cfg']=[
'WP_PATH'=>'./wordpress', // wp安装路径
'WX_APPID'=>'',
'WX_APPSECRET'=>''
];
//以上3个选项可以在index.site-config.php这个自定义文件中重定义
// index.site-config.php 文件代码仓库中是没有的，需要自已新建，把上面几行拷过去，然后修改为正确的配置
if ( file_exists( 'index.site-config.php' ) )
	include_once(  'index.site-config.php' );

//===================
define('WP_USE_THEMES', true);


if(!isset($_GET['code'])) {
  /* Loads the WordPress */
  require($GLOBALS['cfg']['WP_PATH'] . '/wp-blog-header.php');
} else {
  require_once( $GLOBALS['cfg']['WP_PATH'] . '/wp-load.php' ); 
  require_once ( 'wxpress.php' );  

  $code = $_GET['code'];
  WXPRESS::wx_oauth($code);
}