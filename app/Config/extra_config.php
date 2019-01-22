<?php

$env = "development";
$node = "zh_CN";
$debug = false;
$_pre = array();

if ($env == "development") {
    $_pre['site_url'] = "http://xiangce.max.com";
    $_pre['language'] = "'zh_CN";
    $_pre['remote_login_url'] = "http://cn.max.com";
    $_pre['maker_admin_url'] = "http://cn.max.com/myspeed";

    $_pre['upload_dir_root'] = "/var/www/html/xiangce.max.com/app/webroot/uploads/";
    $_pre['upload_url_root'] = "/uploads/";

    //$_pre['cookie_domain'] = ".max.cn";
} elseif ($env == "product") {
    $_pre['site_url'] = "http://xiangce.speed-trade.com.cn";
    $_pre['language'] = "'zh_CN";
    $_pre['remote_login_url'] = "http://www.speed-trade.com.cn";
    $_pre['maker_admin_url'] = "http://www.speed-trade.com.cn/myspeed";

    $_pre['upload_dir_root'] = "/images/";
    $_pre['upload_url_root'] = "/";

   // $_pre['cookie_domain'] = ".speed-trade.com.cn";
} else {
    die("CAN NOT LOAD CONFIG PARAMS!");
}

if ($node == 'zh_CN') {
    $_pre['images_default_host'] = "China";
} elseif ($node == 'jp') {
    $_pre['images_default_host'] = "Japan";
} else {
    $_pre['images_default_host'] = "China";
}


$config['site_url'] = $_pre['site_url'];
$config['Config.language'] = $_pre['language'];
$config['remote_login_url'] = $_pre['remote_login_url'];
$config['maker_admin_url'] = $_pre['maker_admin_url'];
$config['images_default_host'] = $_pre['images_default_host'];
//$config['cookie_domain'] = $_pre['cookie_domain'];
$config['images_hosts'] = array('China' => 'http://images.speed-trade.com.cn', 'Japan' => 'http://images.speed-trade.com');
$config['images_hosts_ssl'] = array('China' => 'https://images.speed-trade.com.cn', 'Japan' => 'https://images.speed-trade.com');

$config['upload_dir_root'] = $_pre['upload_dir_root'];
$config['upload_url_root'] = $_pre['upload_url_root'];
//$config['Session'] = array('cookie' => 'STMAKERSID2', 'ini' => array('session.cookie_domain' => $_pre['cookie_domain']));

$config['debug'] = $debug ? 2 : 0;