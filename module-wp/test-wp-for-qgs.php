<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
	<head>
		<meta charset="UTF-8">
    <title>wp for qgs user test page</title>
  </head>
  <body>
 <?php

 //1, 先定义wordpress安装目录
$GLOBALS['wp_cfg']=[
'WP_PATH'=>'/data/wwwroot/linjp.cn/wordpress', // wp安装路径
];

//2, 定义 WX_KEY
define('WX_KEY','weixin_unionid');

//3, 引用共享文件
require_once(  '../share-code/wordpress/wp-for-qgs-login.php' );

echo '<pre>';

$t=time();
$user_info=[
  'openid'=>"QGSID-$t",
  'unionid'=>"QGSUID-$t",
  'nickname'=>"QGSNK-$t",
  'headimgurl'=>""
];
var_dump($user_info);

//4, 调用此函数
$r=qgs_user_login($user_info);
var_dump($r);


?>
  <a href="/">如果运行成功，则该用户已自动登录wordpress，可点此打开wordpress确认。</a>
  </body>
</html>