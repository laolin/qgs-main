<?php

$GLOBALS['wp_cfg']=[
'WP_PATH'=>'/data/wwwroot/linjp.cn/wordpress', // wp°²×°Â·¾¶
];


define('SHOW_DEBUG_INFO',0);

require_once(  '../share-code/wordpress/wp-for-qgs-login.php' );

echo '<pre>';

$t=time();
$user_info=[
  'openid'=>"QGSID-$t",
  'unionid'=>"QGSUID-$t",
  'nickname'=>"QGSNK-$t",
  'headimgurl'=>""
];
$r=qgs_user_login($user_info);
var_dump($r);
