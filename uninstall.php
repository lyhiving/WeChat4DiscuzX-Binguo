<?php
/**
 *	[公众微信智能云平台(cloud_wx.{})] (C)2013-2099 Powered by YangLin.
 * Author QQ:28945763  问题解答技术交流QQ群：294440459
 *	Version: 5.5       http://i.binguo.me
 *	Date: 2013-4-4 00:00
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require DISCUZ_ROOT.'./config/config_global.php';
$table_a = $_config['db']['1']['tablepre']."cloud_wx";
$table_b = $_config['db']['1']['tablepre']."cloud_wx_pic";

$sql = <<<EOF
DROP TABLE IF EXISTS `$table_a`;
DROP TABLE IF EXISTS `$table_b`;
EOF;

runquery($sql);
$finish = true;
?>