<?php


include_once(  'index.site-config.php' );

//===================
define('WP_USE_THEMES', true);


if(!isset($_GET['code'])) {
  /* Loads the WordPress */
  require($GLOBALS['cfg']['WP_PATH'] . '/wp-blog-header.php');
} else {
  require_once( $GLOBALS['cfg']['WP_PATH'] . '/wp-load.php' ); 
  require_once ( 'src/wxpress.php' );  

  $code = $_GET['code'];
  WXPRESS::wx_oauth($code);
}