<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
	<head>
		<meta charset="UTF-8">
    <title>wp for qgs user test page</title>
  </head>
  <body>
 <?php

 //1, 先定义wordpress安装目录
define('WORDPRESS_PATH',$_SERVER['DOCUMENT_ROOT'] . '/wordpress'); // wp安装路径


//此行可删除，定义这个可显示一些DEBUG信息
define('SHOW_DEBUG_INFO',1);

//2, 引用共享文件
require_once( $_SERVER['DOCUMENT_ROOT'] . '/share-code/wordpress/qgs_share.php' );

echo '<pre>';

$t=time();
$user_info=[
  'openid'=>"QGSID-$t",
  'unionid'=>"QGSUID-$t",
  'nickname'=>"QGSNK-$t",
  'headimgurl'=>""
];
var_dump($user_info);

//3, 调用此函数
$r=QgsShare::checkLoginUnionid();
echo "(checkLoginUnionid return $r) ";

//3, 调用此函数
$r=QgsShare::loginByUserinfo($user_info);
var_dump($r);


?>
  <a href="/">如果运行成功，则该用户已自动登录wordpress，可点此打开wordpress确认。</a>
  </body>
</html>